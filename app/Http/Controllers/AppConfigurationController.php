<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AppConfiguration;
use App\Http\Requests\AppConfiguration\StoreAppConfigurationRequest;
use App\Http\Requests\AppConfiguration\SearchAppConfigurationRequest;
use App\Http\Requests\AppConfiguration\UpdateAppConfigurationRequest;
use App\Http\Resources\AppConfiguration\AppConfigurationListResource;
use App\Http\Resources\AppConfiguration\AppConfigurationShowResource;

class AppConfigurationController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/users",
     *     summary="Get a list of users",
     *     tags={"Users"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $per_page = ($request->per_page > 100) ? 10 : $request->per_page;

        return AppConfigurationListResource::collection(AppConfiguration::orderByDesc('created_at')->paginate($per_page));
    }

    public function search(SearchAppConfigurationRequest $request)
    {
        $code = $request->code;
        $per_page = $request->per_page ?? 10;

        $app_configurations = AppConfiguration::orderByDesc('created_at');

        if ($code) {
            $app_configurations = $app_configurations->where('code', 'LIKE', '%' . $code . '%');
        }

        return AppConfigurationListResource::collection($app_configurations->paginate($per_page));
    }

    public function store(StoreAppConfigurationRequest $request)
    {
        $app_configuration = AppConfiguration::create($request->all());

        return (new AppConfigurationShowResource($app_configuration))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AppConfiguration $app_configuration)
    {
        return new AppConfigurationShowResource($app_configuration);
    }

    public function update(UpdateAppConfigurationRequest $request, AppConfiguration $app_configuration)
    {
        $app_configuration->update($request->all());

        return (new AppConfigurationShowResource($app_configuration->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(AppConfiguration $app_configuration)
    {
        $this->checkGate('app_configuration_delete');

        $app_configuration->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
