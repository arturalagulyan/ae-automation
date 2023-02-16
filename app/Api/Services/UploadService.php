<?php

namespace Api\Services;

use Api\Filters\UploadFilter;
use Api\Repositories\UserRepository;
use Api\Transformers\UploadTransformer;
use Api\Validators\UploadValidator;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Renderer\Renderer;
use Renderer\Steps\Nexrender;
use Uploader\Uploader;
use Uploader\UploaderConstants;

/**
 * Class UploadService
 * @package Api\Services
 */
class UploadService extends BaseApiService
{
    /**
     * @var Uploader
     */
    protected Uploader $uploader;

    /**
     * @var Nexrender
     */
    protected Nexrender $nexrender;

    /**
     * UploadService constructor.
     * @param UploadFilter $filter
     * @param UploadValidator $validator
     * @param UserRepository $repository
     * @param UploadTransformer $transformer
     * @param Uploader $uploader
     * @param Nexrender $nexrender
     */
    public function __construct(
        UploadFilter $filter,
        UploadValidator $validator,
        UserRepository $repository,
        UploadTransformer $transformer,
        Uploader $uploader,
        Nexrender $nexrender
    )
    {
        parent::__construct($filter, $validator, $repository, $transformer);

        $this->uploader = $uploader;
        $this->nexrender = $nexrender;
    }


    /**
     * @param array $data
     * @return array
     * @throws Exception
     * @throws ValidationException
     * @throws GuzzleException
     */
    public function upload(array $data): array
    {
        $this->validator
            ->setData($data)
            ->validate('upload');

        $this->repository->startTransaction();

        try {
            $uploadsFolder = renderer_conf('uploads_folder');

            config([
                'filesystems.disks.public.root' => $uploadsFolder
            ]);

            $photoName = sprintf(
                '%s_%s.%s',
                $data['photo_id'],
                $data['photo_number'],
                $data['photo']->extension()
            );

            $data['photo' . $data['photo_number']] = $this->uploader
                ->setDiskKey(UploaderConstants::DISK_PUBLIC)
                ->setStoragePath($data['photo_id'])
                ->setFileName($photoName)
                ->upload($data['photo']);

            $files = $this->uploader->getFilesInDirectory($data['photo_id']);
            $uploadedJson = $uploadsFolder . $data['photo_id'] . '\\upload.json';

            if (count($files) > 1) {
                $data = array_merge(json_decode(File::get($uploadedJson), true), $data);
            }

            File::put($uploadedJson, json_encode($data));

            if (count($files) < 5) {
                return $data;
            }

            $projectsFolder = renderer_conf('projects_folder');
            $json = $projectsFolder . $data['project'] . '.json';

            if (!file_exists($json)) {
                throw new Exception('Wrong project name');
            }

            $config = json_decode(file_get_contents($json), true);
            $config['data'] = $data;
            $config['upload_id'] = $data['photo_id'];
            $config['replication']['json']['assets'] = [
                [
                    'src' => 'file:///' . $data['photo1']['fullPath'],
                    'type' => 'image',
                    'layerName' => 'customer-image_1',
                ],
                [
                    'src' => 'file:///' . $data['photo2']['fullPath'],
                    'type' => 'image',
                    'layerName' => 'customer-image_2',
                ],
                [
                    'src' => 'file:///' . $data['photo3']['fullPath'],
                    'type' => 'image',
                    'layerName' => 'customer-image_3',
                ],
                [
                    'src' => 'file:///' . $data['photo4']['fullPath'],
                    'type' => 'image',
                    'layerName' => 'customer-image_4',
                ]
            ];

            $job = $this->nexrender->setData($config['replication'])->process();
            $config['job_id'] = $job['uid'];

            $uploadedJson = $config['job_id'] . '\\upload.json';
            File::put(renderer_conf('replicate_folder') . $uploadedJson, json_encode($data));

            $this->repository->commitTransaction();

            return $config;
        } catch (Exception $exception) {
            $this->repository->rollBackTransaction();
            throw $exception;
        }
    }
}
