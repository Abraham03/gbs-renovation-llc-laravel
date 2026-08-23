<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Services\ProjectService;
use App\Http\Resources\ProjectResource; 
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    // LEER: Obtener todos los proyectos (con su categoría y galería)
    public function index(Request $request)
    {
        // Leemos el parámetro 'per_page', si no existe, usamos 5 por defecto.
        $perPage = $request->input('per_page', 50);
        
        $projects = Project::with(['category', 'media'])->paginate($perPage);
        return response()->json($projects);
    }

    // CREAR
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->createProjectWithMedia(
            $request->validated(),
            $request->file('thumbnail'),
            $request->file('media') ?? []
        );

        return response()->json([
            'message' => 'Project successfully created.',
            // 3. Envolvemos el modelo individual en un "new ProjectResource"
            'data' => new ProjectResource($project->load(['category', 'media']))
        ], 201);
    }

    // LEER UN SOLO REGISTRO
    public function show(Project $project)
    {
        // 4. Devolvemos el recurso directamente. Laravel automáticamente le pondrá la llave "data"
        return new ProjectResource($project->load(['category', 'media']));
    }

    // ACTUALIZAR
    // 1. Modifica update para enviar la llave 'media'
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $updatedProject = $this->projectService->updateProject(
            $project,
            $request->validated(),
            $request->file('thumbnail'),
            $request->file('media') ?? [] // Le pasamos las nuevas fotos si las hay
        );

        return response()->json([
            'message' => 'Updated project.',
            'data' => new ProjectResource($updatedProject->load(['category', 'media']))
        ], 200);
    }

    // 2. Agrega esta nueva función para borrar la foto
    public function destroyMedia(int $id)
    {
        $this->projectService->deleteMedia($id);
        return response()->json(['message' => 'File deleted successfully'], 200);
    }

    // ELIMINAR
    public function destroy(Project $project)
    {
        $this->projectService->deleteProject($project);

        // Al eliminar no devolvemos el proyecto, así que este queda igual
        return response()->json([
            'message' => 'Project and media files successfully deleted.'
        ], 200);
    }
}