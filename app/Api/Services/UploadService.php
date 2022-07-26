<?php

namespace Api\Services;

use Api\Filters\UploadFilter;
use Api\Repositories\UserRepository;
use Api\Transformers\UploadTransformer;
use Api\Validators\UploadValidator;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
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

            $uploadId = now()->toDateString() . '-' . uniqid();

            $this->uploader->setDiskKey(UploaderConstants::DISK_PUBLIC);
            $this->uploader->setStoragePath($uploadId);

            $data['photo1'] = $this->uploader->upload($data['photo1']);
            $data['photo2'] = $this->uploader->upload($data['photo2']);
            $data['photo3'] = $this->uploader->upload($data['photo3']);
            $data['photo4'] = $this->uploader->upload($data['photo4']);

            $projectsFolder = renderer_conf('projects_folder');
            $json = $projectsFolder . 'notokay-v0_2.json';

            if (!file_exists($json)) {
                throw new Exception('Wrong project name');
            }

            $config = json_decode(file_get_contents($json), true);
            $config['replication']['data'] = $data;
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

            $this->nexrender->setData($config['replication'])->process();

            $this->repository->commitTransaction();

            return $config;
        } catch (Exception $exception) {
            $this->repository->rollBackTransaction();
            throw $exception;
        }
    }
}
