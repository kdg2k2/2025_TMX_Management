<?php
namespace App\Services;

use App\Models\PersonnelFileType;
use App\Repositories\PersonnelFileTypeRepository;

class PersonnelFileTypeService extends BaseService
{
    public function __construct(
        private FileExtensionService $fileExtensionService
    ) {
        $this->repository = app(PersonnelFileTypeRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['extensions'] = $this->fileExtensionService->list();

        return $res;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractRelations($request);
            $data = $this->repository->store($request);
            $this->syncExtensions($data, $extracted['extensions']);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractRelations($request);
            $data = $this->repository->update($request);
            $this->syncExtensions($data, $extracted['extensions']);
        }, true);
    }

    private function extractRelations(array &$request): array
    {
        $fields = [
            'extensions',
        ];

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    public function syncExtensions(PersonnelFileType $personnelFileType, array $ids)
    {
        $this->syncRelationship($personnelFileType, 'type_id', 'extensions', $ids, 'extension_id');
    }

    public function getExtensions(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            $data = $this->repository->findById($id);
            return array_map(function ($item) {
                return $item['extension']['extension'];
            }, $data->toArray()['extensions'] ?? []);
        });
    }
}
