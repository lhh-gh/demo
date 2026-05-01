

# 安装命令

composer create-project hyperf/hyperf-skeleton 项目名


# 启动命令
php bin/hyperf.php start


# 检查数据库连接
php bin/hyperf.php db:show

# composer  命令
composer install --ignore-platform-reqs  忽略php版本



# 热重载模式（开发环境推荐）
php bin/hyperf.php server:watch

# 代码格式化
composer  cs 

composer cs -- --dry-run 只会列出不符合规范的文件，不会修改代码内容。

php hyperf如果修改的地方比较多，需要执行哪些命令？ 
批量改完代码后，只要「类名/路径/注解」有变动，一律走下面 3 条命令，保证运行时、注解、自动加载全部重新生成，避免任何旧缓存导致的奇怪报错。

1. 清掉 Hyperf 的注解/代理/AOP 编译缓存#
# 在项目根目录
rm -rf runtime/container

这条最重要！改注解、改路径、改切点表达式后必须删，否则永远读到旧路由/旧代理类。

2. 重新生成 Composer 自动加载映射#

composer dump-autoload -o

新增/重命名 PHP 文件、改 namespace 后执行，防止 Class not found。

3.（可选）重启/启动服务#
# 开发模式
php bin/hyperf.php start

# 生产守护进程
php bin/hyperf.php server:restart

一条命令行搞定（常用脚本）#
把下面写成 reload.sh 放在项目根，以后改完直接 ./reload.sh：

#!/usr/bin/env bash
set -e
echo "=== 清理缓存 ==="
rm -rf runtime/container
echo "=== 重载自动加载 ==="
composer dump-autoload -o
echo "=== 启动服务 ==="
php bin/hyperf.php start

赋可执行权限：

chmod +x reload.sh


# 检查端口占用：
# 检查 9501 端口（后端）
lsof -i :9501
netstat -tulpn | grep :9501

# 检查 3000 端口（前端）
lsof -i :3000
netstat -tulpn | grep :3000

# 不开启的情况
echo add("5", "10"); // 输出：15，PHP自动把字符串转成整数，不会报错
echo add(2.9, 3.1); // 输出：5，浮点数被自动截断为整数

# 开启之后的情况：
<?php
declare(strict_types=1);

function add(int $a, int $b): int {
return $a + $b;
}
echo add("5", "10"); // 直接抛出 Fatal error: Uncaught TypeError
echo add(2.9, 3.1); // 同样抛出TypeError，不允许浮点数转整数


———                                                                                                                   
                                                                                                                        
  # 1. 定义接口                                                                                                         
                                                                                                                        
  app/Service/UserServiceInterface.php                                                                                  
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  interface UserServiceInterface                                                                                        
  {                                                                                                                     
      public function getUserInfo(int $id): array;                                                                      
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 2. 定义实现类                                                                                                       
                                                                                                                        
  app/Service/UserService.php                                                                                           
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  class UserService implements UserServiceInterface                                                                     
  {                                                                                                                     
      public function getUserInfo(int $id): array                                                                       
      {                                                                                                                 
          return [                                                                                                      
              'id' => $id,                                                                                              
              'name' => '李四',                                                                                         
              'email' => 'lisi@example.com',                                                                            
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 3. 配置依赖绑定                                                                                                     
                                                                                                                        
  config/autoload/dependencies.php                                                                                      
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  return [                                                                                                              
      App\Service\UserServiceInterface::class => App\Service\UserService::class,                                        
  ];                                                                                                                    
                                                                                                                        
  > 这样 Hyperf 容器在遇到 UserServiceInterface 时，就会自动实例化 UserService。                                        
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 4. 构造函数注入方式                                                                                                 
                                                                                                                        
  app/Controller/UserController.php                                                                                     
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use App\Service\UserServiceInterface;                                                                                 
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                          
                                                                                                                        
  #[Controller(prefix: "user")]                                                                                         
  class UserController                                                                                                  
  {                                                                                                                     
      public function __construct(                                                                                      
          protected UserServiceInterface $userService                                                                   
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      #[GetMapping("info/{id}")]                                                                                        
      public function info(int $id): array                                                                              
      {                                                                                                                 
          return [                                                                                                      
              'method' => 'constructor inject with interface',                                                          
              'data' => $this->userService->getUserInfo($id),                                                           
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 5. #[Inject] 注解注入方式                                                                                           
                                                                                                                        
  app/Controller/UserInjectController.php                                                                               
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use App\Service\UserServiceInterface;                                                                                 
  use Hyperf\Di\Annotation\Inject;                                                                                      
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                          
                                                                                                                        
  #[Controller(prefix: "user-inject")]                                                                                  
  class UserInjectController                                                                                            
  {                                                                                                                     
      #[Inject]                                                                                                         
      protected UserServiceInterface $userService;                                                                      
                                                                                                                        
      #[GetMapping("info/{id}")]                                                                                        
      public function info(int $id): array                                                                              
      {                                                                                                                 
          return [                                                                                                      
              'method' => '#[Inject] with interface',                                                                   
              'data' => $this->userService->getUserInfo($id),                                                           
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 6. 访问测试                                                                                                         
                                                                                                                        
  ## 构造函数注入                                                                                                       
                                                                                                                        
  GET /user/info/1                                                                                                      
                                                                                                                        
  返回：                                                                                                                
                                                                                                                        
  {                                                                                                                     
    "method": "constructor inject with interface",                                                                      
    "data": {                                                                                                           
      "id": 1,                                                                                                          
      "name": "李四",                                                                                                   
      "email": "lisi@example.com"                                                                                       
    }                                                                                                                   
  }                                                                                                                     
                                                                                                                        
  ## #[Inject] 注入                                                                                                     
                                                                                                                        
  GET /user-inject/info/2                                                                                               
                                                                                                                        
  返回：                                                                                                                
                                                                                                                        
  {                                                                                                                     
    "method": "#[Inject] with interface",                                                                               
    "data": {                                                                                                           
      "id": 2,                                                                                                          
      "name": "李四",                                                                                                   
      "email": "lisi@example.com"                                                                                       
    }                                                                                                                   
  }


demo 案例

# 安装 验证规则
composer require hyperf/validation



添加中间件
您需要为使用到验证器组件的 Server 在 config/autoload/middlewares.php 配置文件加上一个全局中间件 Hyperf\Validation\Middleware\ValidationMiddleware 的配置，如下为 http Server 加上对应的全局中间件的示例：
<?php
return [
    // 下面的 http 字符串对应 config/autoload/server.php 内每个 server 的 name 属性对应的值，意味着对应的中间件配置仅应用在该 Server 中
    'http' => [
        // 数组内配置您的全局中间件，顺序根据该数组的顺序
        \Hyperf\Validation\Middleware\ValidationMiddleware::class
        // 这里隐藏了其它中间件
    ],
];
、
# 安装 分页器
composer require hyperf/paginator


  - Controller 只收参和返回
  - Request 做基础校验
  - DTO 做数据传输
  - Service 写业务逻辑
  - Repository 负责数据访问
  - ApiResponse 统一返回格式
  - Code 统一错误码