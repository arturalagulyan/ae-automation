<?php

namespace Api\Core\Response;

use Api\Core\Exceptions\AbstractHttpException;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class Response
 * @package Api\Core\Response
 */
class Response
{
    /**
     * @var IlluminateResponse
     */
    protected $response;

    /**
     * Response constructor.
     * @param IlluminateResponse $response
     */
    public function __construct(IlluminateResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @return IlluminateResponse
     */
    public function ok()
    {
        return $this->response
            ->setContent(null)
            ->setStatusCode(200);
    }

    /**
     * @param $content
     * @return IlluminateResponse
     */
    public function make($content)
    {
        return $this->response
            ->setContent([
                'data' => $content,
                'meta' => [

                ],
            ])
            ->setStatusCode(200);
    }

    /**
     * @param Collection $collection
     * @return IlluminateResponse
     */
    public function collection(Collection $collection)
    {
        return $this->make($collection);
    }

    /**
     * @param LengthAwarePaginator $collection
     * @return IlluminateResponse
     */
    public function pagination(LengthAwarePaginator $collection)
    {
        $page = request()->has('page') ? request()->input('page') : 1;
        $perPage = request()->has('perPage') ? request()->input('perPage') : 20;

        $total = $collection->total();
        $totalPages = !$perPage ? 1 : ceil($total/$perPage);

        return $this->response
            ->setContent([
                'data' => $collection,
                'meta' => [
                    'pagination' => [
                        'page' => $page,
                        'perPage' => $perPage,
                        'total' => $total,
                        'totalPages' => $totalPages,
                    ]
                ],
            ])
            ->setStatusCode(200);
    }

    /**
     * @param AbstractHttpException $exception
     * @return IlluminateResponse
     */
    public function exception(AbstractHttpException $exception)
    {
        $response = $exception->getResponse();

        $response->setContent([
            'error' => 'Http Error',
            'code' => $response->getStatusCode(),
            'message' => $response->getContent(),
            'errors' => $exception->getErrors(),
        ]);

        return $response;
    }
}
