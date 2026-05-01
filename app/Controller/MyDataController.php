<?php
declare(strict_types=1);
namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
#[AutoController]
class MyDataController
{

// Hyperf 会自动为此方法生成一个 /mydata/index 的路由，允许通过 GET 或 POST 方式请求
    /**
     * @param RequestInterface $request
     * @return string
     *
     * MyDataController    @AutoController()    /my_data/index
     * MydataController    @AutoController()    /mydata/index
     * MyDataController    @AutoController(prefix="/data")    /data/index
     */
    public function index(RequestInterface $request)
    {
        // 从请求中获得 id 参数
        $id = $request->input('id', 1);
        return (string)$id;
    }
}