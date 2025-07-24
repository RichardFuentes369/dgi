<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prueba;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CrudControllerPrueba extends Controller
{

    public function get(Request $request)
    {
        if ($request->id) {
            $response = $this->getOne($request);
        } else {
            $response = $this->getAll($request);
        }
        return $response;
    }

    public function getAll(Request $request)
    {
        try {
            $request->validate([
                'page' => 'required|numeric',
                'perPage' => 'required|numeric',
                'sortBy' => 'required|string',
                'sortOrder' => 'required|string|in:asc,desc',
            ]);

            $consulta = Prueba::query();
            $consulta->orderBy($request->sortBy, $request->sortOrder);
            $items = $consulta->paginate($request->perPage, ['*'], 'page', $request->currentPage);

            $nextPageUrl = $items->nextPageUrl();
            $prevPageUrl = $items->previousPageUrl();

            $extractPageNumber = function (?string $url): ?int {
                if (!$url) {
                    return null;
                }
                $urlComponents = parse_url($url);

                if (!isset($urlComponents['query'])) {
                    return null;
                }

                parse_str($urlComponents['query'], $queryParams);

                return isset($queryParams['page']) && is_numeric($queryParams['page'])
                    ? (int) $queryParams['page']
                    : null;
            };

            $nextPageNumber = $extractPageNumber($nextPageUrl);
            $prevPageNumber = $extractPageNumber($prevPageUrl);

            return response()->json([
                'status' => true,
                'message' => 'Items encontrados.',
                'data' => $items->items(),
                'pagination' => [
                    'total' => $items->total(),
                    'per_page' => $items->perPage(),
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                    'links' => [
                        'first_page_url' => 1,
                        'last_page_url' => $items->lastPage(),
                        'next_page_url' => $nextPageNumber,
                        'prev_page_url' => $prevPageNumber,
                    ],
                ],
            ], 200);
        } catch (ValidationException $e) {
            $validationErrors = $e->errors();
            return $validationErrors;
        }
    }

    public function getOne(Request $request)
    {
        $modelo = Prueba::class;
        try {
            $request->validate([
                'id' => 'required|numeric'
            ]);

            if ($request->id) {
                $consulta = $modelo::where('id', '=', $request->id)->first();
                $response = response()->json([
                    'status' => true,
                    'data' => $consulta
                ], 200);
            } else {
                $response = response()->json([
                    'status' => false,
                    'data' => "Recuerde que debe enviar un parametro denomidado id, y este debe ser númerico"
                ], 404);
            }
            return $response;
        } catch (ValidationException $e) {
            $validationErrors = $e->errors();
            if (isset($validationErrors['id'])) {
                return response()->json([
                    'status' => false,
                    'message' => $validationErrors['id'][0] ?? 'El ID debe ser numérico y es requerido.',
                    'errors' => $validationErrors
                ], 400);
            }
        }
    }

    public function save(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:prueba,email',
                'nickname' => 'required|string|max:255',
            ]);

            $newPrueba = Prueba::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Registro guardado exitosamente!',
                'data' => $newPrueba 
            ], 201); 

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422); 
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
    public function putOne(Request $request)
    {
        try {
            $request->validate([
                'id' => 'numeric', 
            ]);
            $prueba = Prueba::findOrFail($request->id);

            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'email' => [
                    'nullable',
                    'string',
                    'email',
                    'max:255',
                    'unique:prueba,email,' . $prueba->id . ',id',
                ],
                'nickname' => 'nullable|string|max:255',
            ]);

            $prueba->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Registro actualizado satisfactoriamente!',
                'data' => $prueba 
            ], 200);

        } catch (ValidationException $e) {
            $validationErrors = $e->errors();
            if (isset($validationErrors['id'])) {
                return response()->json([
                    'status' => false,
                    'message' => $validationErrors['id'][0] ?? 'El ID debe ser numérico y es requerido.',
                    'errors' => $validationErrors
                ], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registro no encontrado por id.',
            ], 404); 
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500); 
        }
    }
    public function deleteOne(Request $request)
    {
        try {
            $request->validate([
                'id' => 'numeric', 
            ]);
            $prueba = Prueba::findOrFail($request->id);
            $prueba->delete();

            return response()->json([
                'status' => true,
                'message' => 'Registro actualizado satisfactoriamente!',
                'data' => $prueba 
            ], 200);

        } catch (ValidationException $e) {
            $validationErrors = $e->errors();
            if (isset($validationErrors['id'])) {
                return response()->json([
                    'status' => false,
                    'message' => $validationErrors['id'][0] ?? 'El ID debe ser numérico y es requerido.',
                    'errors' => $validationErrors
                ], 400);
            }
        }
    }
}
