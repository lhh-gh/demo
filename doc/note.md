

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


依赖绑定的作用：                                                                                                      
  告诉 Hyperf 容器：                                                                                                    
                                                                                                                        
  > “当代码需要某个接口/抽象时，具体应该实例化哪个实现类。”                                                             
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  App\Service\UserServiceInterface::class => App\Service\UserService::class                                             
                                                                                                                        
  意思就是：                                                                                                            
                                                                                                                        
  > 看到 UserServiceInterface，就注入 UserService                                                                       
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 1. 为什么要依赖绑定                                                                                                 
                                                                                                                        
  因为接口本身不能直接 new。                                                                                            
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  interface UserServiceInterface                                                                                        
  {                                                                                                                     
      public function getUserInfo(int $id): array;                                                                      
  }                                                                                                                     
                                                                                                                        
  你写注入时：                                                                                                          
                                                                                                                        
  public function __construct(                                                                                          
      protected UserServiceInterface $userService                                                                       
  ) {                                                                                                                   
  }                                                                                                                     
                                                                                                                        
  Hyperf 容器会想：                                                                                                     
                                                                                                                        
  > 你要的是 UserServiceInterface，                                                                                     
  > 但它只是接口，                                                                                                      
  > 我不知道该用哪个具体类来创建。                                                                                      
                                                                                                                        
  这时候就需要 dependencies.php 告诉它映射关系。                                                                        
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 2. 有依赖绑定时                                                                                                     
                                                                                                                        
  配置：                                                                                                                
                                                                                                                        
  return [                                                                                                              
      App\Service\UserServiceInterface::class => App\Service\UserService::class,                                        
  ];                                                                                                                    
                                                                                                                        
  那么容器就知道：                                                                                                      
                                                                                                                        
  UserServiceInterface  ->  UserService                                                                                 
                                                                                                                        
  于是可以正常自动注入。                                                                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 3. 如果没有依赖绑定，会出现什么问题                                                                                 
                                                                                                                        
  如果你这样写：                                                                                                        
                                                                                                                        
  public function __construct(                                                                                          
      protected UserServiceInterface $userService                                                                       
  ) {                                                                                                                   
  }                                                                                                                     
                                                                                                                        
  但 dependencies.php 没有：                                                                                            
                                                                                                                        
  App\Service\UserServiceInterface::class => App\Service\UserService::class                                             
                                                                                                                        
  那么通常会报类似错误：                                                                                                
                                                                                                                        
  Target [App\Service\UserServiceInterface] is not instantiable                                                         
                                                                                                                        
  或者意思接近的报错：                                                                                                  
                                                                                                                        
  Interface App\Service\UserServiceInterface is not instantiable                                                        
                                                                                                                        
  本质就是：                                                                                                            
                                                                                                                        
  > 容器只能识别到你要“接口”，                                                                                          
  > 但接口没法直接实例化，                                                                                              
  > 又没人告诉它该用哪个实现类。                                                                                        
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 4. 哪些情况可以不写依赖绑定                                                                                         
                                                                                                                        
  ## 情况 1：你直接注入具体类                                                                                           
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  public function __construct(                                                                                          
      protected UserService $userService                                                                                
  ) {                                                                                                                   
  }                                                                                                                     
                                                                                                                        
  这种情况下，如果 UserService 本身可以正常实例化，通常不需要依赖绑定。                                                 
                                                                                                                        
  因为容器可以直接：                                                                                                    
                                                                                                                        
  new UserService(...)                                                                                                  
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  ## 情况 2：没有接口，只有普通类                                                                                       
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  class UserService                                                                                                     
  {                                                                                                                     
  }                                                                                                                     
                                                                                                                        
  直接注入这个类，也通常不需要绑定。                                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 5. 哪些情况必须写依赖绑定                                                                                           
                                                                                                                        
  以下情况一般都要写：                                                                                                  
                                                                                                                        
  ## 1）注入接口                                                                                                        
                                                                                                                        
  protected UserServiceInterface $userService                                                                           
                                                                                                                        
  ## 2）注入抽象类                                                                                                      
                                                                                                                        
  protected BaseUserService $userService                                                                                
                                                                                                                        
  ## 3）你想切换不同实现                                                                                                
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  UserServiceInterface -> MysqlUserService                                                                              
  UserServiceInterface -> RedisUserService                                                                              
                                                                                                                        
  这时候绑定就是切换实现的关键。                                                                                        
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 6. 一个直观例子                                                                                                     
                                                                                                                        
  ## 没有绑定                                                                                                           
                                                                                                                        
  interface UserRepositoryInterface {}                                                                                  
  class UserRepository implements UserRepositoryInterface {}                                                            
                                                                                                                        
  class UserService                                                                                                     
  {                                                                                                                     
      public function __construct(                                                                                      
          protected UserRepositoryInterface $repo                                                                       
      ) {}                                                                                                              
  }                                                                                                                     
                                                                                                                        
  容器看到：                                                                                                            
                                                                                                                        
  UserRepositoryInterface                                                                                               
                                                                                                                        
  会懵：                                                                                                                
                                                                                                                        
  > 这是接口啊，实例化不了。                                                                                            
                                                                                                                        
  所以报错。                                                                                                            
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  ## 加上绑定                                                                                                           
                                                                                                                        
  return [                                                                                                              
      UserRepositoryInterface::class => UserRepository::class,                                                          
  ];                                                                                                                    
                                                                                                                        
  容器就明白了：                                                                                                        
                                                                                                                        
  > 哦，原来要 UserRepository                                                                                           
                                                                                                                        
  于是就能正常注入。                                                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 7. 依赖绑定的核心价值                                                                                               
                                                                                                                        
  不只是“防止报错”，更重要的是：                                                                                        
                                                                                                                        
  ## 解耦                                                                                                               
                                                                                                                        
  控制器和服务只依赖接口，不依赖具体实现。                                                                              
                                                                                                                        
  ## 易扩展                                                                                                             
                                                                                                                        
  以后想把：                                                                                                            
                                                                                                                        
  UserService                                                                                                           
                                                                                                                        
  换成：                                                                                                                
                                                                                                                        
  NewUserService                                                                                                        
                                                                                                                        
  只改绑定，不用大面积改业务代码。                                                                                      
                                                                                                                        
  ## 易测试                                                                                                             
                                                                                                                        
  测试时可以把真实实现替换成 mock 实现。                                                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 8. 一句话理解                                                                                                       
                                                                                                                        
  ## 没有依赖绑定时                                                                                                     
                                                                                                                        
  容器知道“你要什么类型”，                                                                                              
  但不知道接口该对应哪个实现类。                                                                                        
                                                                                                                        
  ## 有依赖绑定时                                                                                                       
                                                                                                                        
  容器就知道：                                                                                                          
                                                                                                                        
  > “这个接口，实际用这个类来创建对象。”                                                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 9. 最后给你一个判断规则                                                                                             
                                                                                                                        
  你看到注入的是：                                                                                                      
                                                                                                                        
  ## 具体类                                                                                                             
                                                                                                                        
  protected UserService $userService                                                                                    
                                                                                                                        
  通常 可以不绑定                                                                                                       
                                                                                                                        
  ## 接口                                                                                                               
                                                                                                                        
  protected UserServiceInterface $userService                                                                           
                                                                                                                        
  通常 必须绑定

# Hyperf 容器自动注入底层原理
# 一、先一句话概括                                                                                                    
                                                                                                                        
  Hyperf 的自动注入本质是：                                                                                             
                                                                                                                        
  > 容器通过反射读取类的依赖信息，再递归创建依赖对象，最后把对象注入进去。                                              
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 二、先看最常见代码                                                                                                  
                                                                                                                        
  class UserController                                                                                                  
  {                                                                                                                     
      public function __construct(                                                                                      
          protected UserServiceInterface $userService                                                                   
      ) {                                                                                                               
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  当 Hyperf 要创建 UserController 时，它不会直接傻乎乎地：                                                              
                                                                                                                        
  new UserController();                                                                                                 
                                                                                                                        
  因为构造函数要求传入：                                                                                                
                                                                                                                        
  UserServiceInterface $userService                                                                                     
                                                                                                                        
  所以容器会先分析：                                                                                                    
                                                                                                                        
  > “构造函数参数是什么类型？”                                                                                          
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 三、底层第一步：反射读取构造函数                                                                                    
                                                                                                                        
  容器大致会做类似这样的事（伪代码）：                                                                                  
                                                                                                                        
  $reflectionClass = new ReflectionClass(UserController::class);                                                        
  $constructor = $reflectionClass->getConstructor();                                                                    
  $params = $constructor->getParameters();                                                                              
                                                                                                                        
  拿到参数：                                                                                                            
                                                                                                                        
  $userService                                                                                                          
                                                                                                                        
  再看参数类型：                                                                                                        
                                                                                                                        
  UserServiceInterface                                                                                                  
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 四、底层第二步：判断这个类型能不能直接实例化                                                                        
                                                                                                                        
  容器会判断：                                                                                                          
                                                                                                                        
  ## 情况 1：如果是普通具体类                                                                                           
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  UserService                                                                                                           
                                                                                                                        
  那它可以继续反射它，再 new 出来。                                                                                     
                                                                                                                        
  ## 情况 2：如果是接口 / 抽象类                                                                                        
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  UserServiceInterface                                                                                                  
                                                                                                                        
  接口不能直接实例化：                                                                                                  
                                                                                                                        
  new UserServiceInterface(); // 错                                                                                     
                                                                                                                        
  所以容器必须去查 依赖绑定表。                                                                                         
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 五、依赖绑定表的作用                                                                                                
                                                                                                                        
  比如你配置了：                                                                                                        
                                                                                                                        
  return [                                                                                                              
      App\Service\UserServiceInterface::class => App\Service\UserService::class,                                        
  ];                                                                                                                    
                                                                                                                        
  容器一查就知道：                                                                                                      
                                                                                                                        
  UserServiceInterface -> UserService                                                                                   
                                                                                                                        
  于是它不再尝试实例化接口，而是去实例化：                                                                              
                                                                                                                        
  UserService                                                                                                           
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 六、底层第三步：递归解析依赖                                                                                        
                                                                                                                        
  问题还没结束。                                                                                                        
                                                                                                                        
  因为 UserService 自己可能也有依赖：                                                                                   
                                                                                                                        
  class UserService implements UserServiceInterface                                                                     
  {                                                                                                                     
      public function __construct(                                                                                      
          protected UserRepositoryInterface $userRepository                                                             
      ) {                                                                                                               
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  容器继续分析：                                                                                                        
                                                                                                                        
  - UserService 依赖 UserRepositoryInterface                                                                            
  - 查绑定表                                                                                                            
  - 得到 UserRepository                                                                                                 
  - 再去创建 UserRepository                                                                                             
                                                                                                                        
  这就是 递归依赖解析。                                                                                                 
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 七、整个创建链路长什么样                                                                                            
                                                                                                                        
  比如你有：                                                                                                            
                                                                                                                        
  UserController                                                                                                        
    -> UserServiceInterface                                                                                             
        -> UserService                                                                                                  
            -> UserRepositoryInterface                                                                                  
                -> UserRepository                                                                                       
                                                                                                                        
  容器大致干的事是：                                                                                                    
                                                                                                                        
  创建 UserController                                                                                                   
    发现需要 UserServiceInterface                                                                                       
      查绑定 => UserService                                                                                             
      创建 UserService                                                                                                  
        发现需要 UserRepositoryInterface                                                                                
          查绑定 => UserRepository                                                                                      
          创建 UserRepository                                                                                           
        用 UserRepository 实例化 UserService                                                                            
    用 UserService 实例化 UserController                                                                                
                                                                                                                        
  最后等价于：                                                                                                          
                                                                                                                        
  $repo = new UserRepository();                                                                                         
  $service = new UserService($repo);                                                                                    
  $controller = new UserController($service);                                                                           
                                                                                                                        
  只是这些都是容器自动完成的。                                                                                          
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 八、为什么“具体类”通常不用绑定                                                                                      
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  class UserService                                                                                                     
  {                                                                                                                     
      public function test() {}                                                                                         
  }                                                                                                                     
                                                                                                                        
  控制器里写：                                                                                                          
                                                                                                                        
  public function __construct(                                                                                          
      protected UserService $userService                                                                                
  ) {                                                                                                                   
  }                                                                                                                     
                                                                                                                        
  因为 UserService 是具体类，容器可以直接：                                                                             
                                                                                                                        
  1. 反射 UserService                                                                                                   
  2. 看它构造函数有没有依赖                                                                                             
  3. 没有就直接 new UserService()                                                                                       
  4. 有就继续递归解析                                                                                                   
                                                                                                                        
  所以不需要绑定。                                                                                                      
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 九、为什么“接口”必须绑定                                                                                            
                                                                                                                        
  接口只是规范：                                                                                                        
                                                                                                                        
  interface UserServiceInterface                                                                                        
  {                                                                                                                     
      public function getUserInfo(int $id): array;                                                                      
  }                                                                                                                     
                                                                                                                        
  它没有实现，不能被实例化。                                                                                            
                                                                                                                        
  也就是说：                                                                                                            
                                                                                                                        
  new UserServiceInterface();                                                                                           
                                                                                                                        
  这在 PHP 里本身就不合法。                                                                                             
                                                                                                                        
  所以容器必须有人告诉它：                                                                                              
                                                                                                                        
  > 这个接口对应哪个具体实现类。                                                                                        
                                                                                                                        
  这就是绑定的根本原因。                                                                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十、没有绑定时为什么会报错                                                                                          
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  public function __construct(                                                                                          
      protected UserServiceInterface $userService                                                                       
  ) {                                                                                                                   
  }                                                                                                                     
                                                                                                                        
  容器解析到这里时，会尝试处理 UserServiceInterface。                                                                   
                                                                                                                        
  但它发现：                                                                                                            
                                                                                                                        
  - 这是接口                                                                                                            
  - 没有绑定                                                                                                            
  - 不知道该用谁                                                                                                        
                                                                                                                        
  于是就会报：                                                                                                          
                                                                                                                        
  is not instantiable                                                                                                   
                                                                                                                        
  意思就是：                                                                                                            
                                                                                                                        
  > 这个东西不能被实例化。                                                                                              
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十一、#[Inject] 注入底层是怎么回事                                                                                  
                                                                                                                        
  你写：                                                                                                                
                                                                                                                        
  #[Inject]                                                                                                             
  protected UserServiceInterface $userService;                                                                          
                                                                                                                        
  和构造函数注入不同，它不是通过构造函数传参，而是：                                                                    
                                                                                                                        
  > 对象先创建，再由容器把属性填充进去。                                                                                
                                                                                                                        
  可以粗暴理解成：                                                                                                      
                                                                                                                        
  $controller = new UserController();                                                                                   
  $controller->userService = $container->get(UserServiceInterface::class);                                              
                                                                                                                        
  当然真实实现更复杂，但核心思路差不多。                                                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十二、构造函数注入 vs #[Inject] 注入底层区别                                                                        
                                                                                                                        
  ## 1）构造函数注入                                                                                                    
                                                                                                                        
  在对象 创建时 就把依赖传进去：                                                                                        
                                                                                                                        
  new UserController($userService);                                                                                     
                                                                                                                        
  特点：                                                                                                                
                                                                                                                        
  - 依赖明确                                                                                                            
  - 对象一创建就是完整可用状态                                                                                          
  - 更适合测试                                                                                                          
  - 更符合面向对象设计                                                                                                  
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  ## 2）#[Inject] 属性注入                                                                                              
                                                                                                                        
  先创建对象，再注入属性：                                                                                              
                                                                                                                        
  $controller = new UserController();                                                                                   
  $controller->userService = $userService;                                                                              
                                                                                                                        
  特点：                                                                                                                
                                                                                                                        
  - 写起来快                                                                                                            
  - 依赖没那么显式                                                                                                      
  - 属性可能在构造函数里还不可用                                                                                        
  - 大项目里一般不如构造函数注入清晰                                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十三、为什么构造函数注入更推荐                                                                                      
                                                                                                                        
  因为它有一个重要特性：                                                                                                
                                                                                                                        
  ## 依赖显式                                                                                                           
                                                                                                                        
  看构造函数就知道这个类需要什么。                                                                                      
                                                                                                                        
  public function __construct(                                                                                          
      protected UserServiceInterface $userService                                                                       
  ) {}                                                                                                                  
                                                                                                                        
  别人一眼就知道：                                                                                                      
                                                                                                                        
  > 这个类依赖 UserServiceInterface                                                                                     
                                                                                                                        
  而 #[Inject] 方式，依赖分散在属性上，不如构造函数集中。                                                               
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十四、容器里还有“单例/共享实例”的概念                                                                               
                                                                                                                        
  容器除了“帮你创建”，通常还会管理对象生命周期。                                                                        
                                                                                                                        
  比如某些服务会被注册成共享实例，容器可能不是每次都 new，而是：                                                        
                                                                                                                        
  - 第一次创建                                                                                                          
  - 后面重复复用                                                                                                        
                                                                                                                        
  你可以理解为容器不只是“工厂”，还是“对象管理中心”。                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十五、一个完整心智模型                                                                                              
                                                                                                                        
  你可以把 Hyperf 容器理解成一个 高级工厂经理：                                                                         
                                                                                                                        
  你说：                                                                                                                
                                                                                                                        
  > 我要一个 UserController                                                                                             
                                                                                                                        
  它不会只看表面，而是会追问：                                                                                          
                                                                                                                        
  1. UserController 要什么？                                                                                            
  2. 它要 UserServiceInterface                                                                                          
  3. 这个接口对应谁？                                                                                                   
  4. 哦，对应 UserService                                                                                               
  5. UserService 又要什么？                                                                                             
  6. 它要 UserRepositoryInterface                                                                                       
  7. 这个接口对应谁？                                                                                                   
  8. 哦，对应 UserRepository                                                                                            
  9. 先把最底层造出来                                                                                                   
  10. 再一层层组装回去                                                                                                  
                                                                                                                        
  最后把完整对象交给你。                                                                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十六、再用一句最直白的话总结                                                                                        
                                                                                                                        
  ## 自动注入的本质                                                                                                     
                                                                                                                        
  不是魔法，                                                                                                            
  而是：                                                                                                                
                                                                                                                        
  - 反射拿依赖                                                                                                          
  - 容器找实现                                                                                                          
  - 递归创建对象                                                                                                        
  - 注入到目标类                                                                                                        
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 十七、你现在最该记住的 4 条                                                                                         
                                                                                                                        
  ## 1                                                                                                                  
                                                                                                                        
  注入 具体类，通常可以直接自动解析。                                                                                   
                                                                                                                        
  ## 2                                                                                                                  
                                                                                                                        
  注入 接口/抽象类，通常必须配置依赖绑定。                                                                              
                                                                                                                        
  ## 3                                                                                                                  
                                                                                                                        
  构造函数注入是在 创建对象时注入。                                                                                     
                                                                                                                        
  ## 4                                                                                                                  
                                                                                                                        
  #[Inject] 是在 对象创建后再注入属性。


# 1）安装                                                                                                             
                                                                                                                        
  composer require hyperf/swagger                                                                                       
                                                                                                                        
  如果你还想配合请求校验生成文档，可再装：                                                                              
                                                                                                                        
  composer require hyperf/validation                                                                                    
                                                                                                                        
  hyperf/swagger 包页面也注明了：hyperf/validation 用于 SwaggerRequest。                                                
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 2）发布配置                                                                                                         
                                                                                                                        
  php bin/hyperf.php vendor:publish hyperf/swagger                                                                      
                                                                                                                        
  发布后一般会生成：                                                                                                    
                                                                                                                        
  config/autoload/swagger.php                                                                                           
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 3）基础配置示例                                                                                                     
                                                                                                                        
  config/autoload/swagger.php                                                                                           
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  use Hyperf\Swagger\Config;                                                                                            
                                                                                                                        
  return [                                                                                                              
      'default' => new Config([                                                                                         
          'title' => 'Demo API Docs',                                                                                   
          'description' => 'Hyperf 3.1 Swagger 示例',                                                                   
          'version' => '1.0.0',                                                                                         
          'scan' => [                                                                                                   
              'paths' => [                                                                                              
                  BASE_PATH . '/app',                                                                                   
              ],                                                                                                        
          ],                                                                                                            
      ]),                                                                                                               
  ];                                                                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 4）写一个 Controller 示例                                                                                           
                                                                                                                        
  app/Controller/UserController.php                                                                                     
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                          
  use OpenApi\Attributes as OA;                                                                                         
                                                                                                                        
  #[OA\Info(                                                                                                            
      version: '1.0.0',                                                                                                 
      title: 'Demo API',                                                                                                
      description: 'Hyperf 3.1 Swagger Demo'                                                                            
  )]                                                                                                                    
  #[OA\Tag(name: 'User', description: '用户模块')]                                                                      
  #[Controller(prefix: 'users')]                                                                                        
  class UserController                                                                                                  
  {                                                                                                                     
      #[OA\Get(                                                                                                         
          path: '/users/{id}',                                                                                          
          operationId: 'getUserById',                                                                                   
          summary: '获取用户详情',                                                                                      
          tags: ['User'],                                                                                               
          parameters: [                                                                                                 
              new OA\Parameter(                                                                                         
                  name: 'id',                                                                                           
                  description: '用户ID',                                                                                
                  in: 'path',                                                                                           
                  required: true,                                                                                       
                  schema: new OA\Schema(type: 'integer', format: 'int64')                                               
              )                                                                                                         
          ],                                                                                                            
          responses: [                                                                                                  
              new OA\Response(                                                                                          
                  response: 200,                                                                                        
                  description: '成功',                                                                                  
                  content: new OA\JsonContent(                                                                          
                      properties: [                                                                                     
                          new OA\Property(property: 'code', type: 'integer', example: 0),                               
                          new OA\Property(property: 'message', type: 'string', example: 'success'),                     
                          new OA\Property(                                                                              
                              property: 'data',                                                                         
                              properties: [                                                                             
                                  new OA\Property(property: 'id', type: 'integer', example: 1),                         
                                  new OA\Property(property: 'name', type: 'string', example: '张三'),                   
                                  new OA\Property(property: 'email', type: 'string', example: 'zhangsan@example.com'),  
                              ],                                                                                        
                              type: 'object'                                                                            
                          ),                                                                                            
                      ],                                                                                                
                      type: 'object'                                                                                    
                  )                                                                                                     
              )                                                                                                         
          ]                                                                                                             
      )]                                                                                                                
      #[GetMapping('{id:\d+}')]                                                                                         
      public function show(int $id): array                                                                              
      {                                                                                                                 
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => 'success',                                                                                   
              'data' => [                                                                                               
                  'id' => $id,                                                                                          
                  'name' => '张三',                                                                                     
                  'email' => 'zhangsan@example.com',                                                                    
              ],                                                                                                        
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 5）启动服务                                                                                                         
                                                                                                                        
  php bin/hyperf.php start                                                                                              
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 6）访问 Swagger 页面                                                                                                
                                                                                                                        
  常见访问地址一般是：                                                                                                  
                                                                                                                        
  http://127.0.0.1:9501/swagger                                                                                         
                                                                                                                        
  有些项目配置后也可能是：                                                                                              
                                                                                                                        
  http://127.0.0.1:9501/swagger/index.html                                                                              
                                                                                                                        
  如果你打开不了，就检查 swagger.php 发布后的路由配置。                                                                 
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 7）常见写法                                                                                                         
                                                                                                                        
  ## GET 参数                                                                                                           
                                                                                                                        
  new OA\Parameter(                                                                                                     
      name: 'keyword',                                                                                                  
      in: 'query',                                                                                                      
      required: false,                                                                                                  
      schema: new OA\Schema(type: 'string')                                                                             
  )                                                                                                                     
                                                                                                                        
  ## POST Body                                                                                                          
                                                                                                                        
  new OA\RequestBody(                                                                                                   
      required: true,                                                                                                   
      content: new OA\JsonContent(                                                                                      
          required: ['name', 'email'],                                                                                  
          properties: [                                                                                                 
              new OA\Property(property: 'name', type: 'string'),                                                        
              new OA\Property(property: 'email', type: 'string'),                                                       
              new OA\Property(property: 'age', type: 'integer'),                                                        
          ]                                                                                                             
      )                                                                                                                 
  )                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 8）POST 新增用户示例                                                                                                
                                                                                                                        
  #[OA\Post(                                                                                                            
      path: '/users',                                                                                                   
      summary: '新增用户',                                                                                              
      tags: ['User'],                                                                                                   
      requestBody: new OA\RequestBody(                                                                                  
          required: true,                                                                                               
          content: new OA\JsonContent(                                                                                  
              required: ['name', 'email'],                                                                              
              properties: [                                                                                             
                  new OA\Property(property: 'name', type: 'string', example: '李四'),                                   
                  new OA\Property(property: 'email', type: 'string', example: 'lisi@example.com'),                      
                  new OA\Property(property: 'age', type: 'integer', example: 20),                                       
              ]                                                                                                         
          )                                                                                                             
      ),                                                                                                                
      responses: [                                                                                                      
          new OA\Response(                                                                                              
              response: 200,                                                                                            
              description: '成功'                                                                                       
          )                                                                                                             
      ]                                                                                                                 
  )]                                                                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 9）你最容易踩的坑                                                                                                   
                                                                                                                        
  ## 1. 没有扫描到目录                                                                                                  
                                                                                                                        
  确认：                                                                                                                
                                                                                                                        
  'scan' => [                                                                                                           
      'paths' => [                                                                                                      
          BASE_PATH . '/app',                                                                                           
      ],                                                                                                                
  ],                                                                                                                    
                                                                                                                        
  ## 2. 注解/属性命名空间写错                                                                                           
                                                                                                                        
  要用：                                                                                                                
                                                                                                                        
  use OpenApi\Attributes as OA;                                                                                         
                                                                                                                        
  不是随便写别的。                                                                                                      
                                                                                                                        
  ## 3. 页面能开，但没有接口                                                                                            
                                                                                                                        
  通常是：                                                                                                              
                                                                                                                        
  - Controller 没被扫描到                                                                                               
  - OA\Get / OA\Post 没写                                                                                               
  - 写法不符合 swagger-php 4.x                                                                                          
                                                                                                                        
  ## 4. 路由和文档 path 不一致                                                                                          
                                                                                                                        
  比如路由是：                                                                                                          
                                                                                                                        
  #[GetMapping('{id:\d+}')]                                                                                             
                                                                                                                        
  文档里最好写：                                                                                                        
                                                                                                                        
  path: '/users/{id}'                                                                                                   
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 10）建议你在 Hyperf 3.1 里这样用                                                                                    
                                                                                                                        
  推荐组合：                                                                                                            
                                                                                                                        
  - 路由：#[Controller] + #[GetMapping]                                                                                 
  - 文档：OpenApi\Attributes as OA                                                                                      
  - 请求体：OA\RequestBody                                                                                              
  - 返回体：OA\Response + OA\JsonContent                                                                                
                                                                                                                        
  这是和 hyperf/swagger + swagger-php 4.x 更匹配的写法。


## 1. 安装                                                                                                                                                                       
                                                                                                                                                                                   
  composer require hyperf/redis                                                                                                                                                    
                                                                                                                                                                                   
  hyperf/redis 文档说明：它提供 Redis 协程客户端与连接池。                                                                                                                         
  来源：Hyperf 文档（Redis 协程客户端）                                                                                                                                            
  https://geekdaxue.co/read/hyperf-3.0-doc/docs-zh-cn-redis.md                                                                                                                     
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 2. 配置                                                                                                                                                                       
                                                                                                                                                                                   
  文件：config/autoload/redis.php                                                                                                                                                  
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  return [                                                                                                                                                                         
      'default' => [                                                                                                                                                               
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                                                                                
          'auth' => env('REDIS_AUTH', null),                                                                                                                                       
          'port' => (int) env('REDIS_PORT', 6379),                                                                                                                                 
          'db' => (int) env('REDIS_DB', 0),                                                                                                                                        
          'pool' => [                                                                                                                                                              
              'min_connections' => 1,                                                                                                                                              
              'max_connections' => 10,                                                                                                                                             
              'connect_timeout' => 10.0,                                                                                                                                           
              'wait_timeout' => 3.0,                                                                                                                                               
              'heartbeat' => -1,                                                                                                                                                   
              'max_idle_time' => 60.0,                                                                                                                                             
          ],                                                                                                                                                                       
          'options' => [                                                                                                                                                           
          ],                                                                                                                                                                       
      ],                                                                                                                                                                           
  ];                                                                                                                                                                               
                                                                                                                                                                                   
  .env                                                                                                                                                                             
                                                                                                                                                                                   
  REDIS_HOST=127.0.0.1                                                                                                                                                             
  REDIS_PORT=6379                                                                                                                                                                  
  REDIS_AUTH=                                                                                                                                                                      
  REDIS_DB=0                                                                                                                                                                       
                                                                                                                                                                                   
  参考：                                                                                                                                                                           
  https://geekdaxue.co/read/hyperf-3.0-doc/docs-zh-cn-redis.md                                                                                                                     
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 3. 基本使用                                                                                                                                                                   
                                                                                                                                                                                   
  ### 方式 1：依赖注入                                                                                                                                                             
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Controller;                                                                                                                                                        
                                                                                                                                                                                   
  use Hyperf\Di\Annotation\Inject;                                                                                                                                                 
  use Hyperf\HttpServer\Annotation\Controller;                                                                                                                                     
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                                                                                     
  use Hyperf\Redis\Redis;                                                                                                                                                          
                                                                                                                                                                                   
  #[Controller(prefix: 'redis')]                                                                                                                                                   
  class RedisController                                                                                                                                                            
  {                                                                                                                                                                                
      #[Inject]                                                                                                                                                                    
      protected Redis $redis;                                                                                                                                                      
                                                                                                                                                                                   
      #[GetMapping('set')]                                                                                                                                                         
      public function set(): array                                                                                                                                                 
      {                                                                                                                                                                            
          $this->redis->set('name', 'zhangsan');                                                                                                                                   
                                                                                                                                                                                   
          return [                                                                                                                                                                 
              'code' => 200,                                                                                                                                                       
              'message' => 'success',                                                                                                                                              
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
                                                                                                                                                                                   
      #[GetMapping('get')]                                                                                                                                                         
      public function get(): array                                                                                                                                                 
      {                                                                                                                                                                            
          $value = $this->redis->get('name');                                                                                                                                      
                                                                                                                                                                                   
          return [                                                                                                                                                                 
              'code' => 200,                                                                                                                                                       
              'data' => $value,                                                                                                                                                    
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 方式 2：容器获取                                                                                                                                                             
                                                                                                                                                                                   
  use Hyperf\Utils\ApplicationContext;                                                                                                                                             
  use Hyperf\Redis\Redis;                                                                                                                                                          
                                                                                                                                                                                   
  $container = ApplicationContext::getContainer();                                                                                                                                 
  $redis = $container->get(Redis::class);                                                                                                                                          
                                                                                                                                                                                   
  $redis->set('foo', 'bar');                                                                                                                                                       
  $value = $redis->get('foo');                                                                                                                                                     
                                                                                                                                                                                   
  文档说明 Hyperf\Redis\Redis 实际是 \Redis 的代理对象。                                                                                                                           
  来源：                                                                                                                                                                           
  https://geekdaxue.co/read/hyperf-3.0-doc/docs-zh-cn-redis.md                                                                                                                     
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 4. 常用命令示例                                                                                                                                                               
                                                                                                                                                                                   
  ### 字符串                                                                                                                                                                       
                                                                                                                                                                                   
  $this->redis->set('token', 'abc123');                                                                                                                                            
  $this->redis->get('token');                                                                                                                                                      
  $this->redis->del('token');                                                                                                                                                      
  $this->redis->expire('token', 3600);                                                                                                                                             
                                                                                                                                                                                   
  ### 哈希                                                                                                                                                                         
                                                                                                                                                                                   
  $this->redis->hSet('user:1', 'name', '张三');                                                                                                                                    
  $this->redis->hSet('user:1', 'email', 'zhangsan@example.com');                                                                                                                   
  $this->redis->hGet('user:1', 'name');                                                                                                                                            
  $this->redis->hGetAll('user:1');                                                                                                                                                 
                                                                                                                                                                                   
  ### 列表                                                                                                                                                                         
                                                                                                                                                                                   
  $this->redis->lPush('queue', 'job1');                                                                                                                                            
  $this->redis->lPush('queue', 'job2');                                                                                                                                            
  $this->redis->rPop('queue');                                                                                                                                                     
                                                                                                                                                                                   
  ### 集合                                                                                                                                                                         
                                                                                                                                                                                   
  $this->redis->sAdd('tags', 'php');                                                                                                                                               
  $this->redis->sAdd('tags', 'hyperf');                                                                                                                                            
  $this->redis->sMembers('tags');                                                                                                                                                  
                                                                                                                                                                                   
  ### 有序集合                                                                                                                                                                     
                                                                                                                                                                                   
  $this->redis->zAdd('rank', 100, 'user1');                                                                                                                                        
  $this->redis->zAdd('rank', 90, 'user2');                                                                                                                                         
  $this->redis->zRevRange('rank', 0, 10, true);                                                                                                                                    
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 5. 多 Redis 库配置                                                                                                                                                            
                                                                                                                                                                                   
  config/autoload/redis.php                                                                                                                                                        
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  return [                                                                                                                                                                         
      'default' => [                                                                                                                                                               
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                                                                                
          'port' => (int) env('REDIS_PORT', 6379),                                                                                                                                 
          'auth' => env('REDIS_AUTH', null),                                                                                                                                       
          'db' => 0,                                                                                                                                                               
          'pool' => [                                                                                                                                                              
              'min_connections' => 1,                                                                                                                                              
              'max_connections' => 10,                                                                                                                                             
              'connect_timeout' => 10.0,                                                                                                                                           
              'wait_timeout' => 3.0,                                                                                                                                               
              'heartbeat' => -1,                                                                                                                                                   
              'max_idle_time' => 60.0,                                                                                                                                             
          ],                                                                                                                                                                       
      ],                                                                                                                                                                           
                                                                                                                                                                                   
      'cache' => [                                                                                                                                                                 
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                                                                                
          'port' => (int) env('REDIS_PORT', 6379),                                                                                                                                 
          'auth' => env('REDIS_AUTH', null),                                                                                                                                       
          'db' => 1,                                                                                                                                                               
          'pool' => [                                                                                                                                                              
              'min_connections' => 1,                                                                                                                                              
              'max_connections' => 10,                                                                                                                                             
              'connect_timeout' => 10.0,                                                                                                                                           
              'wait_timeout' => 3.0,                                                                                                                                               
              'heartbeat' => -1,                                                                                                                                                   
              'max_idle_time' => 60.0,                                                                                                                                             
          ],                                                                                                                                                                       
      ],                                                                                                                                                                           
  ];                                                                                                                                                                               
                                                                                                                                                                                   
  如果你要使用非默认连接，一般通过工厂获取。文档有“多库配置”说明。                                                                                                                 
  来源：                                                                                                                                                                           
  https://geekdaxue.co/read/hyperf-3.0-doc/docs-zh-cn-redis.md                                                                                                                     
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 6. 设置序列化选项                                                                                                                                                             
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  return [                                                                                                                                                                         
      'default' => [                                                                                                                                                               
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                                                                                
          'auth' => env('REDIS_AUTH', null),                                                                                                                                       
          'port' => (int) env('REDIS_PORT', 6379),                                                                                                                                 
          'db' => (int) env('REDIS_DB', 0),                                                                                                                                        
          'pool' => [                                                                                                                                                              
              'min_connections' => 1,                                                                                                                                              
              'max_connections' => 10,                                                                                                                                             
              'connect_timeout' => 10.0,                                                                                                                                           
              'wait_timeout' => 3.0,                                                                                                                                               
              'heartbeat' => -1,                                                                                                                                                   
              'max_idle_time' => 60.0,                                                                                                                                             
          ],                                                                                                                                                                       
          'options' => [                                                                                                                                                           
              Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP,                                                                                                                      
          ],                                                                                                                                                                       
      ],                                                                                                                                                                           
  ];                                                                                                                                                                               
                                                                                                                                                                                   
  来源：                                                                                                                                                                           
  https://geekdaxue.co/read/hyperf-3.0-doc/docs-zh-cn-redis.md                                                                                                                     
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 7. 结合缓存组件使用                                                                                                                                                           
                                                                                                                                                                                   
  如果你想把 Redis 当缓存用，可以安装：                                                                                                                                            
                                                                                                                                                                                   
  composer require hyperf/cache                                                                                                                                                    
                                                                                                                                                                                   
  默认缓存驱动就是 Redis。                                                                                                                                                         
  来源：Hyperf Cache 文档                                                                                                                                                          
  https://geekdaxue.co/read/hyperf-3.0-doc/docs-zh-cn-cache.md                                                                                                                     
                                                                                                                                                                                   
  简单示例：                                                                                                                                                                       
                                                                                                                                                                                   
  use Psr\SimpleCache\CacheInterface;                                                                                                                                              
                                                                                                                                                                                   
  class DemoService                                                                                                                                                                
  {                                                                                                                                                                                
      public function __construct(protected CacheInterface $cache)                                                                                                                 
      {                                                                                                                                                                            
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function test(): mixed                                                                                                                                                
      {                                                                                                                                                                            
          $this->cache->set('user:1', ['name' => '张三'], 3600);                                                                                                                   
                                                                                                                                                                                   
          return $this->cache->get('user:1');                                                                                                                                      
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 8. 常见问题                                                                                                                                                                   
                                                                                                                                                                                   
  ### 1）报 Redis 扩展相关错误                                                                                                                                                     
                                                                                                                                                                                   
  Hyperf Redis 基于 ext-redis，需要确认 PHP 已安装并启用 Redis 扩展。                                                                                                              
                                                                                                                                                                                   
  ### 2）连接失败                                                                                                                                                                  
                                                                                                                                                                                   
  先测试 Redis 是否可连：                                                                                                                                                          
                                                                                                                                                                                   
  redis-cli -h 127.0.0.1 -p 6379                                                                                                                                                   
                                                                                                                                                                                   
  ### 3）协程环境里不要自己长期持有底层连接                                                                                                                                        
                                                                                                                                                                                   
  正常注入 Hyperf\Redis\Redis 用即可，连接池会帮你管理。                                                                                                                           
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 9. 最小 Demo                                                                                                                                                                  
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Controller;                                                                                                                                                        
                                                                                                                                                                                   
  use Hyperf\Di\Annotation\Inject;                                                                                                                                                 
  use Hyperf\HttpServer\Annotation\Controller;                                                                                                                                     
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                                                                                     
  use Hyperf\Redis\Redis;                                                                                                                                                          
                                                                                                                                                                                   
  #[Controller(prefix: 'redis')]                                                                                                                                                   
  class RedisController                                                                                                                                                            
  {                                                                                                                                                                                
      #[Inject]                                                                                                                                                                    
      protected Redis $redis;                                                                                                                                                      
                                                                                                                                                                                   
      #[GetMapping('demo')]                                                                                                                                                        
      public function demo(): array                                                                                                                                                
      {                                                                                                                                                                            
          $this->redis->set('site', 'hyperf', 60);                                                                                                                                 
                                                                                                                                                                                   
          return [                                                                                                                                                                 
              'value' => $this->redis->get('site'),                                                                                                                                
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  访问：                                                                                                                                                                           
                                                                                                                                                                                   
  GET /redis/demo

下面直接给你一套 Hyperf + Redis 实战 Demo，包括：                                                                                                                                
                                                                                                                                                                                   
  1. 封装成 RedisService 的企业写法                                                                                                                                                
  2. 短信验证码 Demo                                                                                                                                                               
  3. 分布式锁 Demo                                                                                                                                                                 
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 一、RedisService 企业写法                                                                                                                                                      
                                                                                                                                                                                   
  ## 1）配置文件                                                                                                                                                                   
                                                                                                                                                                                   
  config/autoload/redis.php                                                                                                                                                        
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  return [                                                                                                                                                                         
      'default' => [                                                                                                                                                               
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                                                                                
          'auth' => env('REDIS_AUTH', null),                                                                                                                                       
          'port' => (int) env('REDIS_PORT', 6379),                                                                                                                                 
          'db' => (int) env('REDIS_DB', 0),                                                                                                                                        
          'pool' => [                                                                                                                                                              
              'min_connections' => 1,                                                                                                                                              
              'max_connections' => 20,                                                                                                                                             
              'connect_timeout' => 10.0,                                                                                                                                           
              'wait_timeout' => 3.0,                                                                                                                                               
              'heartbeat' => -1,                                                                                                                                                   
              'max_idle_time' => 60.0,                                                                                                                                             
          ],                                                                                                                                                                       
          'options' => [],                                                                                                                                                         
      ],                                                                                                                                                                           
  ];                                                                                                                                                                               
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 2）封装 RedisService                                                                                                                                                          
                                                                                                                                                                                   
  app/Service/RedisService.php                                                                                                                                                     
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Service;                                                                                                                                                           
                                                                                                                                                                                   
  use Hyperf\Redis\Redis;                                                                                                                                                          
  use Hyperf\Di\Annotation\Inject;                                                                                                                                                 
                                                                                                                                                                                   
  class RedisService                                                                                                                                                               
  {                                                                                                                                                                                
      #[Inject]                                                                                                                                                                    
      protected Redis $redis;                                                                                                                                                      
                                                                                                                                                                                   
      public function set(string $key, mixed $value, int $ttl = 0): bool                                                                                                           
      {                                                                                                                                                                            
          $value = is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);                                                                              
                                                                                                                                                                                   
          if ($ttl > 0) {                                                                                                                                                          
              return (bool) $this->redis->set($key, $value, $ttl);                                                                                                                 
          }                                                                                                                                                                        
                                                                                                                                                                                   
          return (bool) $this->redis->set($key, $value);                                                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function get(string $key, bool $decodeJson = false): mixed                                                                                                            
      {                                                                                                                                                                            
          $value = $this->redis->get($key);                                                                                                                                        
                                                                                                                                                                                   
          if ($value === false || $value === null) {                                                                                                                               
              return null;                                                                                                                                                         
          }                                                                                                                                                                        
                                                                                                                                                                                   
          if ($decodeJson) {                                                                                                                                                       
              return json_decode($value, true);                                                                                                                                    
          }                                                                                                                                                                        
                                                                                                                                                                                   
          return $value;                                                                                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function delete(string $key): int                                                                                                                                     
      {                                                                                                                                                                            
          return $this->redis->del($key);                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function exists(string $key): bool                                                                                                                                    
      {                                                                                                                                                                            
          return (bool) $this->redis->exists($key);                                                                                                                                
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function expire(string $key, int $ttl): bool                                                                                                                          
      {                                                                                                                                                                            
          return (bool) $this->redis->expire($key, $ttl);                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function incr(string $key, int $by = 1): int                                                                                                                          
      {                                                                                                                                                                            
          return $by === 1 ? $this->redis->incr($key) : $this->redis->incrBy($key, $by);                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function ttl(string $key): int                                                                                                                                        
      {                                                                                                                                                                            
          return $this->redis->ttl($key);                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function getClient(): Redis                                                                                                                                           
      {                                                                                                                                                                            
          return $this->redis;                                                                                                                                                     
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 二、短信验证码 Demo                                                                                                                                                            
                                                                                                                                                                                   
  ## 1）短信验证码 Service                                                                                                                                                         
                                                                                                                                                                                   
  app/Service/SmsCodeService.php                                                                                                                                                   
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Service;                                                                                                                                                           
                                                                                                                                                                                   
  class SmsCodeService                                                                                                                                                             
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected RedisService $redisService                                                                                                                                     
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function sendCode(string $mobile): array                                                                                                                              
      {                                                                                                                                                                            
          $sendLockKey = "sms:lock:{$mobile}";                                                                                                                                     
          $codeKey = "sms:code:{$mobile}";                                                                                                                                         
                                                                                                                                                                                   
          if ($this->redisService->exists($sendLockKey)) {                                                                                                                         
              return [                                                                                                                                                             
                  'code' => 429,                                                                                                                                                   
                  'message' => '发送过于频繁，请稍后再试',                                                                                                                         
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          $code = (string) random_int(100000, 999999);                                                                                                                             
                                                                                                                                                                                   
          $this->redisService->set($codeKey, $code, 300);                                                                                                                          
          $this->redisService->set($sendLockKey, 1, 60);                                                                                                                           
                                                                                                                                                                                   
          return [                                                                                                                                                                 
              'code' => 0,                                                                                                                                                         
              'message' => '验证码发送成功',                                                                                                                                       
              'data' => [                                                                                                                                                          
                  'mobile' => $mobile,                                                                                                                                             
                  'code' => $code,                                                                                                                                                 
                  'expire' => 300,                                                                                                                                                 
              ],                                                                                                                                                                   
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function verifyCode(string $mobile, string $code): array                                                                                                              
      {                                                                                                                                                                            
          $codeKey = "sms:code:{$mobile}";                                                                                                                                         
          $cachedCode = $this->redisService->get($codeKey);                                                                                                                        
                                                                                                                                                                                   
          if (! $cachedCode) {                                                                                                                                                     
              return [                                                                                                                                                             
                  'code' => 400,                                                                                                                                                   
                  'message' => '验证码已过期',                                                                                                                                     
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          if ($cachedCode !== $code) {                                                                                                                                             
              return [                                                                                                                                                             
                  'code' => 400,                                                                                                                                                   
                  'message' => '验证码错误',                                                                                                                                       
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          $this->redisService->delete($codeKey);                                                                                                                                   
                                                                                                                                                                                   
          return [                                                                                                                                                                 
              'code' => 0,                                                                                                                                                         
              'message' => '验证码校验成功',                                                                                                                                       
              'data' => null,                                                                                                                                                      
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 2）控制器                                                                                                                                                                     
                                                                                                                                                                                   
  app/Controller/SmsController.php                                                                                                                                                 
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Controller;                                                                                                                                                        
                                                                                                                                                                                   
  use App\Service\SmsCodeService;                                                                                                                                                  
  use Hyperf\HttpServer\Annotation\Controller;                                                                                                                                     
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                                                                                     
                                                                                                                                                                                   
  #[Controller(prefix: 'sms')]                                                                                                                                                     
  class SmsController                                                                                                                                                              
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected SmsCodeService $smsCodeService                                                                                                                                 
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      #[GetMapping('send')]                                                                                                                                                        
      public function send(): array                                                                                                                                                
      {                                                                                                                                                                            
          $mobile = '13800138000';                                                                                                                                                 
                                                                                                                                                                                   
          return $this->smsCodeService->sendCode($mobile);                                                                                                                         
      }                                                                                                                                                                            
                                                                                                                                                                                   
      #[GetMapping('verify')]                                                                                                                                                      
      public function verify(): array                                                                                                                                              
      {                                                                                                                                                                            
          $mobile = '13800138000';                                                                                                                                                 
          $code = '123456';                                                                                                                                                        
                                                                                                                                                                                   
          return $this->smsCodeService->verifyCode($mobile, $code);                                                                                                                
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 3）实际业务中的 Redis Key 设计                                                                                                                                                
                                                                                                                                                                                   
  sms:code:13800138000   // 验证码，5分钟                                                                                                                                          
  sms:lock:13800138000   // 发送锁，60秒                                                                                                                                           
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 三、Redis 分布式锁 Demo                                                                                                                                                        
                                                                                                                                                                                   
  ## 1）分布式锁 Service                                                                                                                                                           
                                                                                                                                                                                   
  app/Service/RedisLockService.php                                                                                                                                                 
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Service;                                                                                                                                                           
                                                                                                                                                                                   
  use Hyperf\Redis\Redis;                                                                                                                                                          
                                                                                                                                                                                   
  class RedisLockService                                                                                                                                                           
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected RedisService $redisService                                                                                                                                     
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function acquire(string $key, string $token, int $ttl = 10): bool                                                                                                     
      {                                                                                                                                                                            
          /** @var Redis $redis */                                                                                                                                                 
          $redis = $this->redisService->getClient();                                                                                                                               
                                                                                                                                                                                   
          return (bool) $redis->set($key, $token, ['nx', 'ex' => $ttl]);                                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function release(string $key, string $token): bool                                                                                                                    
      {                                                                                                                                                                            
          $lua = <<<LUA                                                                                                                                                            
  if redis.call("get", KEYS[1]) == ARGV[1] then                                                                                                                                    
      return redis.call("del", KEYS[1])                                                                                                                                            
  else                                                                                                                                                                             
      return 0                                                                                                                                                                     
  end                                                                                                                                                                              
  LUA;                                                                                                                                                                             
                                                                                                                                                                                   
          $result = $this->redisService->getClient()->eval($lua, [$key, $token], 1);                                                                                               
                                                                                                                                                                                   
          return (int) $result === 1;                                                                                                                                              
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function executeWithLock(string $key, callable $callback, int $ttl = 10): mixed                                                                                       
      {                                                                                                                                                                            
          $token = uniqid('', true);                                                                                                                                               
                                                                                                                                                                                   
          if (! $this->acquire($key, $token, $ttl)) {                                                                                                                              
              return [                                                                                                                                                             
                  'code' => 423,                                                                                                                                                   
                  'message' => '操作过于频繁，请稍后再试',                                                                                                                         
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          try {                                                                                                                                                                    
              return $callback();                                                                                                                                                  
          } finally {                                                                                                                                                              
              $this->release($key, $token);                                                                                                                                        
          }                                                                                                                                                                        
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 2）控制器示例                                                                                                                                                                 
                                                                                                                                                                                   
  app/Controller/OrderController.php                                                                                                                                               
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Controller;                                                                                                                                                        
                                                                                                                                                                                   
  use App\Service\RedisLockService;                                                                                                                                                
  use Hyperf\HttpServer\Annotation\Controller;                                                                                                                                     
  use Hyperf\HttpServer\Annotation\PostMapping;                                                                                                                                    
                                                                                                                                                                                   
  #[Controller(prefix: 'order')]                                                                                                                                                   
  class OrderController                                                                                                                                                            
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected RedisLockService $lockService                                                                                                                                  
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      #[PostMapping('submit')]                                                                                                                                                     
      public function submit(): array                                                                                                                                              
      {                                                                                                                                                                            
          $userId = 1001;                                                                                                                                                          
          $lockKey = "lock:order:submit:{$userId}";                                                                                                                                
                                                                                                                                                                                   
          $result = $this->lockService->executeWithLock($lockKey, function () use ($userId) {                                                                                      
              return [                                                                                                                                                             
                  'code' => 0,                                                                                                                                                     
                  'message' => '下单成功',                                                                                                                                         
                  'data' => [                                                                                                                                                      
                      'user_id' => $userId,                                                                                                                                        
                      'order_no' => 'ORD' . date('YmdHis'),                                                                                                                        
                  ],                                                                                                                                                               
              ];                                                                                                                                                                   
          }, 5);                                                                                                                                                                   
                                                                                                                                                                                   
          return is_array($result) ? $result : [                                                                                                                                   
              'code' => 0,                                                                                                                                                         
              'message' => 'success',                                                                                                                                              
              'data' => $result,                                                                                                                                                   
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 四、适用场景                                                                                                                                                                   
                                                                                                                                                                                   
  ## 1）RedisService                                                                                                                                                               
                                                                                                                                                                                   
  适合统一封装：                                                                                                                                                                   
                                                                                                                                                                                   
  - set/get/delete                                                                                                                                                                 
  - JSON 序列化                                                                                                                                                                    
  - TTL 处理                                                                                                                                                                       
  - 公共 Redis 工具方法                                                                                                                                                            
                                                                                                                                                                                   
  ## 2）短信验证码                                                                                                                                                                 
                                                                                                                                                                                   
  适合：                                                                                                                                                                           
                                                                                                                                                                                   
  - 登录验证码                                                                                                                                                                     
  - 注册验证码                                                                                                                                                                     
  - 找回密码验证码                                                                                                                                                                 
  - 限流发送                                                                                                                                                                       
                                                                                                                                                                                   
  ## 3）分布式锁                                                                                                                                                                   
                                                                                                                                                                                   
  适合：                                                                                                                                                                           
                                                                                                                                                                                   
  - 防止重复下单                                                                                                                                                                   
  - 防止重复支付                                                                                                                                                                   
  - 防止接口并发重复提交                                                                                                                                                           
  - 秒杀/抢购串行控制                                                                                                                                                              
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 五、推荐企业写法建议                                                                                                                                                           
                                                                                                                                                                                   
  ## Redis key 统一管理                                                                                                                                                            
                                                                                                                                                                                   
  可以提取成常量类：                                                                                                                                                               
                                                                                                                                                                                   
  class RedisKey                                                                                                                                                                   
  {                                                                                                                                                                                
      public const SMS_CODE = 'sms:code:%s';                                                                                                                                       
      public const SMS_LOCK = 'sms:lock:%s';                                                                                                                                       
      public const ORDER_SUBMIT_LOCK = 'lock:order:submit:%s';                                                                                                                     
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ## 错误码统一管理                                                                                                                                                                
                                                                                                                                                                                   
  可以配合你前面那个 Code.php：                                                                                                                                                    
                                                                                                                                                                                   
  Code::SUCCESS                                                                                                                                                                    
  Code::TOO_MANY_REQUESTS                                                                                                                                                          
  Code::LOCKED                                                                                                                                                                     
                                                                                                                                                                                   
  ## 响应统一格式                                                                                                                                                                  
                                                                                                                                                                                   
  配合 success()/error() trait 统一输出。

# 1. 短信验证码能看到什么                                                                                             
                                                                                                                        
  例如短信验证码 Demo 里写入：                                                                                          
                                                                                                                        
  sms:code:13800138000                                                                                                  
  sms:lock:13800138000                                                                                                  
                                                                                                                        
  如果调用：                                                                                                            
                                                                                                                        
  $this->redisService->set($codeKey, $code, 300);                                                                       
  $this->redisService->set($sendLockKey, 1, 60);                                                                        
                                                                                                                        
  那么客户端里能看到：                                                                                                  
                                                                                                                        
  ## key                                                                                                                
                                                                                                                        
  sms:code:13800138000                                                                                                  
  sms:lock:13800138000                                                                                                  
                                                                                                                        
  ## value                                                                                                              
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  123456                                                                                                                
  1                                                                                                                     
                                                                                                                        
  ## TTL                                                                                                                
                                                                                                                        
  还能看到剩余过期时间，比如：                                                                                          
                                                                                                                        
  - 300 秒                                                                                                              
  - 60 秒                                                                                                               
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 2. 分布式锁能看到什么                                                                                               
                                                                                                                        
  例如分布式锁写入：                                                                                                    
                                                                                                                        
  lock:order:submit:1001                                                                                                
                                                                                                                        
  值一般是一个 token：                                                                                                  
                                                                                                                        
  uniqid('', true)                                                                                                      
                                                                                                                        
  所以客户端里会看到类似：                                                                                              
                                                                                                                        
  ## key                                                                                                                
                                                                                                                        
  lock:order:submit:1001                                                                                                
                                                                                                                        
  ## value                                                                                                              
                                                                                                                        
  6812f7bc9a1f76.12345678                                                                                               
                                                                                                                        
  ## TTL                                                                                                                
                                                                                                                        
  比如 5 秒、10 秒                                                                                                      
                                                                                                                        
  这个 key 一般生命周期很短，所以你可能要在锁持有期间立刻看，过一会儿就没了。                                           
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 3. 普通 RedisService 存的数据也能看到                                                                               
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  $this->redisService->set('user:1', ['name' => '张三', 'age' => 18], 3600);                                            
                                                                                                                        
  如果你的封装里做了：                                                                                                  
                                                                                                                        
  json_encode($value, JSON_UNESCAPED_UNICODE)                                                                           
                                                                                                                        
  那客户端看到的 value 就会是：                                                                                         
                                                                                                                        
  {"name":"张三","age":18}                                                                                              
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 4. 怎么查看                                                                                                         
                                                                                                                        
  ## 方式 1：redis-cli                                                                                                  
                                                                                                                        
  ### 查看所有 key                                                                                                      
                                                                                                                        
  keys *                                                                                                                
                                                                                                                        
  ### 查看验证码 key                                                                                                    
                                                                                                                        
  keys sms:*                                                                                                            
                                                                                                                        
  ### 查看锁 key                                                                                                        
                                                                                                                        
  keys lock:*                                                                                                           
                                                                                                                        
  ### 查看具体值                                                                                                        
                                                                                                                        
  get sms:code:13800138000                                                                                              
  get sms:lock:13800138000                                                                                              
  get lock:order:submit:1001                                                                                            
                                                                                                                        
  ### 查看剩余过期时间                                                                                                  
                                                                                                                        
  ttl sms:code:13800138000                                                                                              
  ttl lock:order:submit:1001                                                                                            
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  ## 方式 2：图形化客户端                                                                                               
                                                                                                                        
  常见客户端：                                                                                                          
                                                                                                                        
  - Another Redis Desktop Manager                                                                                       
  - RedisInsight                                                                                                        
  - Medis                                                                                                               
  - Redis Desktop Manager                                                                                               
                                                                                                                        
  你连接 Redis 后：                                                                                                     
                                                                                                                        
  - 左侧看 key 列表                                                                                                     
  - 点进去看 value                                                                                                      
  - 一般也能看到 TTL                                                                                                    
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 5. 为什么有时客户端看不到                                                                                           
                                                                                                                        
  有几个常见原因：                                                                                                      
                                                                                                                        
  ## 1）key 已经过期                                                                                                    
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  - 验证码 300 秒                                                                                                       
  - 锁 5 秒                                                                                                             
                                                                                                                        
  过期很快，刷新时可能已经没了。                                                                                        
                                                                                                                        
  ## 2）连的不是同一个 Redis 库                                                                                         
                                                                                                                        
  Redis 有多个 DB，例如：                                                                                               
                                                                                                                        
  databases 16                                                                                                          
                                                                                                                        
  你代码里如果是：                                                                                                      
                                                                                                                        
  'db' => 1                                                                                                             
                                                                                                                        
  而客户端看的是 DB 0，就会觉得“没有数据”。                                                                             
                                                                                                                        
  ### 查看当前库                                                                                                        
                                                                                                                        
  select 0                                                                                                              
  keys *                                                                                                                
                                                                                                                        
  或者：                                                                                                                
                                                                                                                        
  select 1                                                                                                              
  keys *                                                                                                                
                                                                                                                        
  ## 3）连的不是同一台 Redis                                                                                            
                                                                                                                        
  比如：                                                                                                                
                                                                                                                        
  - Hyperf 连的是 WSL 里的 Redis                                                                                        
  - 你客户端连的是 Windows 上另一个 Redis                                                                               
                                                                                                                        
  那当然看不到相同数据。                                                                                                
                                                                                                                        
  ## 4）分布式锁时间太短                                                                                                
                                                                                                                        
  锁只有几秒，很容易刚打开客户端就过期了。                                                                              
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 6. 建议你测试的方法                                                                                                 
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  ## 测试验证码                                                                                                         
                                                                                                                        
  先调用验证码接口，让它写入：                                                                                          
                                                                                                                        
  sms:code:13800138000                                                                                                  
  sms:lock:13800138000                                                                                                  
                                                                                                                        
  然后立刻执行：                                                                                                        
                                                                                                                        
  redis-cli                                                                                                             
                                                                                                                        
  keys sms:*                                                                                                            
  get sms:code:13800138000                                                                                              
  ttl sms:code:13800138000                                                                                              
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  ## 测试分布式锁                                                                                                       
                                                                                                                        
  你可以临时把锁时间改长一点：                                                                                          
                                                                                                                        
  $result = $this->lockService->executeWithLock($lockKey, function () use ($userId) {                                   
      sleep(15);                                                                                                        
                                                                                                                        
      return [                                                                                                          
          'code' => 0,                                                                                                  
          'message' => '下单成功',                                                                                      
          'data' => [                                                                                                   
              'user_id' => $userId,                                                                                     
              'order_no' => 'ORD' . date('YmdHis'),                                                                     
          ],                                                                                                            
      ];                                                                                                                
  }, 30);                                                                                                               
                                                                                                                        
  这样你有 15~30 秒时间去客户端看：                                                                                     
                                                                                                                        
  keys lock:*                                                                                                           
  get lock:order:submit:1001                                                                                            
  ttl lock:order:submit:1001                                                                                            
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 7. 结论                                                                                                             
                                                                                                                        
  ## 可以看到                                                                                                           
                                                                                                                        
  Hyperf 写入 Redis 的数据，Redis 客户端可以直接看到。                                                                  
                                                                                                                        
  ## 能看到哪些                                                                                                         
                                                                                                                        
  - key                                                                                                                 
  - value                                                                                                               
  - ttl                                                                                                                 
  - 所属 db                                                                                                             
                                                                                                                        
  ## 看不到通常是因为                                                                                                   
                                                                                                                        
  - key 过期了                                                                                                          
  - 库不对                                                                                                              
  - Redis 实例不对                                                                                                      
  - 锁时间太短                                                                                                          
                                                                                                                        
  ———
# 1. Redis 基础配置                                                                                                   
                                                                                                                        
  ## 1.1 安装                                                                                                           
                                                                                                                        
  ```bash                                                                                                               
  composer require hyperf/redis                                                                                         
                                                                                                                        
  ## 1.2 配置文件                                                                                                       
                                                                                                                        
  文件：config/autoload/redis.php                                                                                       
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  return [                                                                                                              
      'default' => [                                                                                                    
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                     
          'auth' => env('REDIS_AUTH', null),                                                                            
          'port' => (int) env('REDIS_PORT', 6379),                                                                      
          'db' => (int) env('REDIS_DB', 0),                                                                             
          'pool' => [                                                                                                   
              'min_connections' => 1,                                                                                   
              'max_connections' => 20,                                                                                  
              'connect_timeout' => 10.0,                                                                                
              'wait_timeout' => 3.0,                                                                                    
              'heartbeat' => -1,                                                                                        
              'max_idle_time' => 60.0,                                                                                  
          ],                                                                                                            
          'options' => [],                                                                                              
      ],                                                                                                                
  ];                                                                                                                    
                                                                                                                        
  .env                                                                                                                  
                                                                                                                        
  REDIS_HOST=127.0.0.1                                                                                                  
  REDIS_PORT=6379                                                                                                       
  REDIS_AUTH=                                                                                                           
  REDIS_DB=0                                                                                                            
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 2. RedisService 企业封装                                                                                            
                                                                                                                        
  ## 2.1 作用                                                                                                           
                                                                                                                        
  统一封装：                                                                                                            
                                                                                                                        
  - set/get/delete                                                                                                      
  - exists/expire/incr/ttl                                                                                              
  - JSON 序列化                                                                                                         
  - 获取底层 Redis 客户端                                                                                               
                                                                                                                        
  ## 2.2 示例代码                                                                                                       
                                                                                                                        
  文件：app/Service/RedisService.php                                                                                    
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  use Hyperf\Di\Annotation\Inject;                                                                                      
  use Hyperf\Redis\Redis;                                                                                               
                                                                                                                        
  class RedisService                                                                                                    
  {                                                                                                                     
      #[Inject]                                                                                                         
      protected Redis $redis;                                                                                           
                                                                                                                        
      /**                                                                                                               
       * 写入缓存                                                                                                       
       */                                                                                                               
      public function set(string $key, mixed $value, int $ttl = 0): bool                                                
      {                                                                                                                 
          $value = is_scalar($value)                                                                                    
              ? (string) $value                                                                                         
              : json_encode($value, JSON_UNESCAPED_UNICODE);                                                            
                                                                                                                        
          if ($ttl > 0) {                                                                                               
              return (bool) $this->redis->set($key, $value, $ttl);                                                      
          }                                                                                                             
                                                                                                                        
          return (bool) $this->redis->set($key, $value);                                                                
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 读取缓存                                                                                                       
       */                                                                                                               
      public function get(string $key, bool $decodeJson = false): mixed                                                 
      {                                                                                                                 
          $value = $this->redis->get($key);                                                                             
                                                                                                                        
          if ($value === false || $value === null) {                                                                    
              return null;                                                                                              
          }                                                                                                             
                                                                                                                        
          if ($decodeJson) {                                                                                            
              return json_decode($value, true);                                                                         
          }                                                                                                             
                                                                                                                        
          return $value;                                                                                                
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 删除 key                                                                                                       
       */                                                                                                               
      public function delete(string $key): int                                                                          
      {                                                                                                                 
          return $this->redis->del($key);                                                                               
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 判断 key 是否存在                                                                                              
       */                                                                                                               
      public function exists(string $key): bool                                                                         
      {                                                                                                                 
          return (bool) $this->redis->exists($key);                                                                     
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 设置过期时间                                                                                                   
       */                                                                                                               
      public function expire(string $key, int $ttl): bool                                                               
      {                                                                                                                 
          return (bool) $this->redis->expire($key, $ttl);                                                               
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 自增                                                                                                           
       */                                                                                                               
      public function incr(string $key, int $by = 1): int                                                               
      {                                                                                                                 
          return $by === 1                                                                                              
              ? $this->redis->incr($key)                                                                                
              : $this->redis->incrBy($key, $by);                                                                        
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 获取剩余过期时间                                                                                               
       */                                                                                                               
      public function ttl(string $key): int                                                                             
      {                                                                                                                 
          return $this->redis->ttl($key);                                                                               
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 获取底层 Redis 客户端                                                                                          
       */                                                                                                               
      public function getClient(): Redis                                                                                
      {                                                                                                                 
          return $this->redis;                                                                                          
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 2.3 原理                                                                                                           
                                                                                                                        
  Redis 原生存的是字符串，所以数组/对象要先 json_encode()。                                                             
  统一封装后，业务代码不需要重复处理序列化、TTL、异常值。                                                               
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 3. 短信验证码 Demo                                                                                                  
                                                                                                                        
  ## 3.1 Redis Key 设计                                                                                                 
                                                                                                                        
  sms:code:13800138000                                                                                                  
  sms:lock:13800138000                                                                                                  
                                                                                                                        
  - sms:code:手机号：验证码                                                                                             
  - sms:lock:手机号：发送频率锁                                                                                         
                                                                                                                        
  ## 3.2 Service 代码                                                                                                   
                                                                                                                        
  文件：app/Service/SmsCodeService.php                                                                                  
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  class SmsCodeService                                                                                                  
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisService $redisService                                                                          
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 发送验证码                                                                                                     
       */                                                                                                               
      public function sendCode(string $mobile): array                                                                   
      {                                                                                                                 
          $sendLockKey = "sms:lock:{$mobile}";                                                                          
          $codeKey = "sms:code:{$mobile}";                                                                              
                                                                                                                        
          if ($this->redisService->exists($sendLockKey)) {                                                              
              return [                                                                                                  
                  'code' => 429,                                                                                        
                  'message' => '发送过于频繁，请稍后再试',                                                              
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          $code = (string) random_int(100000, 999999);                                                                  
                                                                                                                        
          $this->redisService->set($codeKey, $code, 300);                                                               
          $this->redisService->set($sendLockKey, 1, 60);                                                                
                                                                                                                        
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => '验证码发送成功',                                                                            
              'data' => [                                                                                               
                  'mobile' => $mobile,                                                                                  
                  'code' => $code,                                                                                      
                  'expire' => 300,                                                                                      
              ],                                                                                                        
          ];                                                                                                            
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 校验验证码                                                                                                     
       */                                                                                                               
      public function verifyCode(string $mobile, string $code): array                                                   
      {                                                                                                                 
          $codeKey = "sms:code:{$mobile}";                                                                              
          $cachedCode = $this->redisService->get($codeKey);                                                             
                                                                                                                        
          if (! $cachedCode) {                                                                                          
              return [                                                                                                  
                  'code' => 400,                                                                                        
                  'message' => '验证码已过期',                                                                          
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          if ($cachedCode !== $code) {                                                                                  
              return [                                                                                                  
                  'code' => 400,                                                                                        
                  'message' => '验证码错误',                                                                            
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          $this->redisService->delete($codeKey);                                                                        
                                                                                                                        
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => '验证码校验成功',                                                                            
              'data' => null,                                                                                           
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 3.3 Controller 代码                                                                                                
                                                                                                                        
  文件：app/Controller/SmsController.php                                                                                
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use App\Service\SmsCodeService;                                                                                       
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                          
                                                                                                                        
  #[Controller(prefix: 'sms')]                                                                                          
  class SmsController                                                                                                   
  {                                                                                                                     
      public function __construct(                                                                                      
          protected SmsCodeService $smsCodeService                                                                      
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      #[GetMapping('send')]                                                                                             
      public function send(): array                                                                                     
      {                                                                                                                 
          $mobile = '13800138000';                                                                                      
                                                                                                                        
          return $this->smsCodeService->sendCode($mobile);                                                              
      }                                                                                                                 
                                                                                                                        
      #[GetMapping('verify')]                                                                                           
      public function verify(): array                                                                                   
      {                                                                                                                 
          $mobile = '13800138000';                                                                                      
          $code = '123456';                                                                                             
                                                                                                                        
          return $this->smsCodeService->verifyCode($mobile, $code);                                                     
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 3.4 原理                                                                                                           
                                                                                                                        
  - 验证码存 5 分钟                                                                                                     
  - 发送锁存 60 秒                                                                                                      
  - 验证成功后立即删除验证码，防止复用                                                                                  
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 4. 分布式锁 Demo                                                                                                    
                                                                                                                        
  ## 4.1 适用场景                                                                                                       
                                                                                                                        
  - 防重复下单                                                                                                          
  - 防重复支付                                                                                                          
  - 防止接口并发重复提交                                                                                                
                                                                                                                        
  ## 4.2 RedisLockService                                                                                               
                                                                                                                        
  文件：app/Service/RedisLockService.php                                                                                
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  use Hyperf\Redis\Redis;                                                                                               
                                                                                                                        
  class RedisLockService                                                                                                
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisService $redisService                                                                          
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 获取锁                                                                                                         
       */                                                                                                               
      public function acquire(string $key, string $token, int $ttl = 10): bool                                          
      {                                                                                                                 
          /** @var Redis $redis */                                                                                      
          $redis = $this->redisService->getClient();                                                                    
                                                                                                                        
          return (bool) $redis->set($key, $token, ['nx', 'ex' => $ttl]);                                                
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 释放锁                                                                                                         
       *                                                                                                                
       * 使用 Lua 保证原子性，避免误删他人锁                                                                            
       */                                                                                                               
      public function release(string $key, string $token): bool                                                         
      {                                                                                                                 
          $lua = <<<LUA                                                                                                 
  if redis.call("get", KEYS[1]) == ARGV[1] then                                                                         
      return redis.call("del", KEYS[1])                                                                                 
  else                                                                                                                  
      return 0                                                                                                          
  end                                                                                                                   
  LUA;                                                                                                                  
                                                                                                                        
          $result = $this->redisService->getClient()->eval($lua, [$key, $token], 1);                                    
                                                                                                                        
          return (int) $result === 1;                                                                                   
      }                                                                                                                 
                                                                                                                        
      /**                                                                                                               
       * 带锁执行业务逻辑                                                                                               
       */                                                                                                               
      public function executeWithLock(string $key, callable $callback, int $ttl = 10): mixed                            
      {                                                                                                                 
          $token = uniqid('', true);                                                                                    
                                                                                                                        
          if (! $this->acquire($key, $token, $ttl)) {                                                                   
              return [                                                                                                  
                  'code' => 423,                                                                                        
                  'message' => '操作过于频繁，请稍后再试',                                                              
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          try {                                                                                                         
              return $callback();                                                                                       
          } finally {                                                                                                   
              $this->release($key, $token);                                                                             
          }                                                                                                             
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 4.3 OrderController 示例                                                                                           
                                                                                                                        
  文件：app/Controller/OrderController.php                                                                              
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use App\Service\RedisLockService;                                                                                     
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\PostMapping;                                                                         
                                                                                                                        
  #[Controller(prefix: 'order')]                                                                                        
  class OrderController                                                                                                 
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisLockService $lockService                                                                       
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      #[PostMapping('submit')]                                                                                          
      public function submit(): array                                                                                   
      {                                                                                                                 
          $userId = 1001;                                                                                               
          $lockKey = "lock:order:submit:{$userId}";                                                                     
                                                                                                                        
          $result = $this->lockService->executeWithLock($lockKey, function () use ($userId) {                           
              return [                                                                                                  
                  'code' => 0,                                                                                          
                  'message' => '下单成功',                                                                              
                  'data' => [                                                                                           
                      'user_id' => $userId,                                                                             
                      'order_no' => 'ORD' . date('YmdHis'),                                                             
                  ],                                                                                                    
              ];                                                                                                        
          }, 5);                                                                                                        
                                                                                                                        
          return is_array($result) ? $result : [                                                                        
              'code' => 0,                                                                                              
              'message' => 'success',                                                                                   
              'data' => $result,                                                                                        
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 4.4 原理                                                                                                           
                                                                                                                        
  - SET key value NX EX ttl：只有 key 不存在时才加锁，并自动过期                                                        
  - token：标识锁归属                                                                                                   
  - Lua：保证释放锁原子性                                                                                               
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 5. Redis Key 命名规范                                                                                               
                                                                                                                        
  ## 5.1 推荐格式                                                                                                       
                                                                                                                        
  模块:动作:标识                                                                                                        
                                                                                                                        
  例如：                                                                                                                
                                                                                                                        
  sms:code:13800138000                                                                                                  
  user:token:1001                                                                                                       
  lock:order:submit:1001                                                                                                
  seckill:stock:1001                                                                                                    
                                                                                                                        
  ## 5.2 常量类示例                                                                                                     
                                                                                                                        
  文件：app/Constants/RedisKey.php                                                                                      
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Constants;                                                                                              
                                                                                                                        
  class RedisKey                                                                                                        
  {                                                                                                                     
      public const SMS_CODE = 'sms:code:%s';                                                                            
      public const SMS_LOCK = 'sms:lock:%s';                                                                            
      public const USER_TOKEN = 'user:token:%s';                                                                        
      public const ORDER_SUBMIT_LOCK = 'lock:order:submit:%s';                                                          
      public const INVENTORY_DEDUCT_LOCK = 'lock:inventory:deduct:sku:%s';                                              
      public const SECKILL_STOCK = 'seckill:stock:%s';                                                                  
      public const SECKILL_USER_ORDER = 'seckill:order:user:%s:%s';                                                     
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 6. 登录/注册业务中的 Redis 实战                                                                                     
                                                                                                                        
  ## 6.1 登录失败次数限制                                                                                               
                                                                                                                        
  ### Key 设计                                                                                                          
                                                                                                                        
  user:login:fail:1001                                                                                                  
  user:login:lock:1001                                                                                                  
                                                                                                                        
  ### 示例代码                                                                                                          
                                                                                                                        
  文件：app/Service/LoginFailService.php                                                                                
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  class LoginFailService                                                                                                
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisService $redisService                                                                          
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      public function recordFail(int $userId): array                                                                    
      {                                                                                                                 
          $failKey = "user:login:fail:{$userId}";                                                                       
          $lockKey = "user:login:lock:{$userId}";                                                                       
                                                                                                                        
          if ($this->redisService->exists($lockKey)) {                                                                  
              return [                                                                                                  
                  'code' => 403,                                                                                        
                  'message' => '账号已被锁定，请稍后再试',                                                              
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          $times = $this->redisService->incr($failKey);                                                                 
                                                                                                                        
          if ($times === 1) {                                                                                           
              $this->redisService->expire($failKey, 600);                                                               
          }                                                                                                             
                                                                                                                        
          if ($times >= 5) {                                                                                            
              $this->redisService->set($lockKey, 1, 600);                                                               
          }                                                                                                             
                                                                                                                        
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => '记录失败次数成功',                                                                          
              'data' => [                                                                                               
                  'fail_times' => $times,                                                                               
              ],                                                                                                        
          ];                                                                                                            
      }                                                                                                                 
                                                                                                                        
      public function clearFail(int $userId): void                                                                      
      {                                                                                                                 
          $this->redisService->delete("user:login:fail:{$userId}");                                                     
          $this->redisService->delete("user:login:lock:{$userId}");                                                     
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 6.2 登录 Token 缓存                                                                                                
                                                                                                                        
  ### Key 设计                                                                                                          
                                                                                                                        
  user:token:1001                                                                                                       
                                                                                                                        
  ### 示例代码                                                                                                          
                                                                                                                        
  文件：app/Service/AuthTokenService.php                                                                                
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  class AuthTokenService                                                                                                
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisService $redisService                                                                          
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      public function createToken(int $userId): array                                                                   
      {                                                                                                                 
          $token = bin2hex(random_bytes(16));                                                                           
          $key = "user:token:{$userId}";                                                                                
                                                                                                                        
          $this->redisService->set($key, $token, 7200);                                                                 
                                                                                                                        
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => '登录成功',                                                                                  
              'data' => [                                                                                               
                  'user_id' => $userId,                                                                                 
                  'token' => $token,                                                                                    
                  'expire' => 7200,                                                                                     
              ],                                                                                                        
          ];                                                                                                            
      }                                                                                                                 
                                                                                                                        
      public function checkToken(int $userId, string $token): bool                                                      
      {                                                                                                                 
          return $this->redisService->get("user:token:{$userId}") === $token;                                           
      }                                                                                                                 
                                                                                                                        
      public function clearToken(int $userId): int                                                                      
      {                                                                                                                 
          return $this->redisService->delete("user:token:{$userId}");                                                   
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 7. 普通库存扣减实战 Demo                                                                                            
                                                                                                                        
  ## 7.1 表结构                                                                                                         
                                                                                                                        
  CREATE TABLE `inventories` (                                                                                          
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,                                                                       
    `sku_id` bigint unsigned NOT NULL,                                                                                  
    `stock` int NOT NULL DEFAULT 0,                                                                                     
    `created_at` datetime DEFAULT NULL,                                                                                 
    `updated_at` datetime DEFAULT NULL,                                                                                 
    PRIMARY KEY (`id`),                                                                                                 
    UNIQUE KEY `uk_sku_id` (`sku_id`)                                                                                   
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;                                                                              
                                                                                                                        
  ## 7.2 Model                                                                                                          
                                                                                                                        
  文件：app/Model/Inventory.php                                                                                         
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Model;                                                                                                  
                                                                                                                        
  use Hyperf\DbConnection\Model\Model;                                                                                  
                                                                                                                        
  class Inventory extends Model                                                                                         
  {                                                                                                                     
      protected ?string $table = 'inventories';                                                                         
                                                                                                                        
      protected array $fillable = [                                                                                     
          'sku_id',                                                                                                     
          'stock',                                                                                                      
      ];                                                                                                                
  }                                                                                                                     
                                                                                                                        
  ## 7.3 InventoryService                                                                                               
                                                                                                                        
  文件：app/Service/InventoryService.php                                                                                
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  use App\Model\Inventory;                                                                                              
  use Hyperf\DbConnection\Db;                                                                                           
                                                                                                                        
  class InventoryService                                                                                                
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisLockService $lockService                                                                       
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      public function deduct(int $skuId, int $quantity): array                                                          
      {                                                                                                                 
          if ($quantity <= 0) {                                                                                         
              return [                                                                                                  
                  'code' => 400,                                                                                        
                  'message' => '扣减数量必须大于 0',                                                                    
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          $lockKey = "lock:inventory:deduct:sku:{$skuId}";                                                              
                                                                                                                        
          return $this->lockService->executeWithLock($lockKey, function () use ($skuId, $quantity) {                    
              return Db::transaction(function () use ($skuId, $quantity) {                                              
                  $inventory = Inventory::query()                                                                       
                      ->where('sku_id', $skuId)                                                                         
                      ->lockForUpdate()                                                                                 
                      ->first();                                                                                        
                                                                                                                        
                  if (! $inventory) {                                                                                   
                      return [                                                                                          
                          'code' => 404,                                                                                
                          'message' => '库存记录不存在',                                                                
                          'data' => null,                                                                               
                      ];                                                                                                
                  }                                                                                                     
                                                                                                                        
                  if ($inventory->stock < $quantity) {                                                                  
                      return [                                                                                          
                          'code' => 400,                                                                                
                          'message' => '库存不足',                                                                      
                          'data' => [                                                                                   
                              'sku_id' => $skuId,                                                                       
                              'stock' => $inventory->stock,                                                             
                              'quantity' => $quantity,                                                                  
                          ],                                                                                            
                      ];                                                                                                
                  }                                                                                                     
                                                                                                                        
                  $inventory->stock -= $quantity;                                                                       
                  $inventory->save();                                                                                   
                                                                                                                        
                  return [                                                                                              
                      'code' => 0,                                                                                      
                      'message' => '扣减库存成功',                                                                      
                      'data' => [                                                                                       
                          'sku_id' => $skuId,                                                                           
                          'deduct_quantity' => $quantity,                                                               
                          'left_stock' => $inventory->stock,                                                            
                      ],                                                                                                
                  ];                                                                                                    
              });                                                                                                       
          }, 5);                                                                                                        
      }                                                                                                                 
                                                                                                                        
      public function add(int $skuId, int $quantity): array                                                             
      {                                                                                                                 
          if ($quantity <= 0) {                                                                                         
              return [                                                                                                  
                  'code' => 400,                                                                                        
                  'message' => '增加数量必须大于 0',                                                                    
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          $lockKey = "lock:inventory:add:sku:{$skuId}";                                                                 
                                                                                                                        
          return $this->lockService->executeWithLock($lockKey, function () use ($skuId, $quantity) {                    
              return Db::transaction(function () use ($skuId, $quantity) {                                              
                  $inventory = Inventory::query()                                                                       
                      ->where('sku_id', $skuId)                                                                         
                      ->lockForUpdate()                                                                                 
                      ->first();                                                                                        
                                                                                                                        
                  if (! $inventory) {                                                                                   
                      $inventory = Inventory::query()->create([                                                         
                          'sku_id' => $skuId,                                                                           
                          'stock' => $quantity,                                                                         
                      ]);                                                                                               
                  } else {                                                                                              
                      $inventory->stock += $quantity;                                                                   
                      $inventory->save();                                                                               
                  }                                                                                                     
                                                                                                                        
                  return [                                                                                              
                      'code' => 0,                                                                                      
                      'message' => '增加库存成功',                                                                      
                      'data' => [                                                                                       
                          'sku_id' => $skuId,                                                                           
                          'add_quantity' => $quantity,                                                                  
                          'stock' => $inventory->stock,                                                                 
                      ],                                                                                                
                  ];                                                                                                    
              });                                                                                                       
          }, 5);                                                                                                        
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 7.4 Controller                                                                                                     
                                                                                                                        
  文件：app/Controller/InventoryController.php                                                                          
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use App\Service\InventoryService;                                                                                     
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\PostMapping;                                                                         
                                                                                                                        
  #[Controller(prefix: 'inventory')]                                                                                    
  class InventoryController                                                                                             
  {                                                                                                                     
      public function __construct(                                                                                      
          protected InventoryService $inventoryService                                                                  
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      #[PostMapping('deduct')]                                                                                          
      public function deduct(): array                                                                                   
      {                                                                                                                 
          return $this->inventoryService->deduct(1, 1);                                                                 
      }                                                                                                                 
                                                                                                                        
      #[PostMapping('add')]                                                                                             
      public function add(): array                                                                                      
      {                                                                                                                 
          return $this->inventoryService->add(1, 5);                                                                    
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 7.5 原理                                                                                                           
                                                                                                                        
  - Redis 锁削峰                                                                                                        
  - MySQL 事务 + lockForUpdate() 兜底                                                                                   
  - 避免超卖                                                                                                            
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 8. 秒杀场景库存扣减 Demo                                                                                            
                                                                                                                        
  ## 8.1 Redis Key 设计                                                                                                 
                                                                                                                        
  seckill:stock:1001                                                                                                    
  seckill:order:user:1001:88                                                                                            
  lock:seckill:product:1001                                                                                             
                                                                                                                        
  ## 8.2 SeckillService                                                                                                 
                                                                                                                        
  文件：app/Service/SeckillService.php                                                                                  
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Service;                                                                                                
                                                                                                                        
  use App\Model\Inventory;                                                                                              
  use Hyperf\DbConnection\Db;                                                                                           
                                                                                                                        
  class SeckillService                                                                                                  
  {                                                                                                                     
      public function __construct(                                                                                      
          protected RedisService $redisService,                                                                         
          protected RedisLockService $lockService                                                                       
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      public function rush(int $productId, int $userId): array                                                          
      {                                                                                                                 
          $stockKey = "seckill:stock:{$productId}";                                                                     
          $userOrderKey = "seckill:order:user:{$productId}:{$userId}";                                                  
          $lockKey = "lock:seckill:product:{$productId}";                                                               
                                                                                                                        
          if ($this->redisService->exists($userOrderKey)) {                                                             
              return [                                                                                                  
                  'code' => 400,                                                                                        
                  'message' => '您已参与过抢购，请勿重复下单',                                                          
                  'data' => null,                                                                                       
              ];                                                                                                        
          }                                                                                                             
                                                                                                                        
          return $this->lockService->executeWithLock($lockKey, function () use (                                        
              $productId,                                                                                               
              $userId,                                                                                                  
              $stockKey,                                                                                                
              $userOrderKey                                                                                             
          ) {                                                                                                           
              if ($this->redisService->exists($userOrderKey)) {                                                         
                  return [                                                                                              
                      'code' => 400,                                                                                    
                      'message' => '您已参与过抢购，请勿重复下单',                                                      
                      'data' => null,                                                                                   
                  ];                                                                                                    
              }                                                                                                         
                                                                                                                        
              $stock = (int) ($this->redisService->get($stockKey) ?? 0);                                                
                                                                                                                        
              if ($stock <= 0) {                                                                                        
                  return [                                                                                              
                      'code' => 400,                                                                                    
                      'message' => '秒杀库存不足',                                                                      
                      'data' => null,                                                                                   
                  ];                                                                                                    
              }                                                                                                         
                                                                                                                        
              $leftStock = $stock - 1;                                                                                  
              $this->redisService->set($stockKey, $leftStock);                                                          
              $this->redisService->set($userOrderKey, 1, 3600);                                                         
                                                                                                                        
              $result = Db::transaction(function () use ($productId, $userId) {                                         
                  $inventory = Inventory::query()                                                                       
                      ->where('sku_id', $productId)                                                                     
                      ->lockForUpdate()                                                                                 
                      ->first();                                                                                        
                                                                                                                        
                  if (! $inventory) {                                                                                   
                      return [                                                                                          
                          'code' => 404,                                                                                
                          'message' => '库存记录不存在',                                                                
                          'data' => null,                                                                               
                      ];                                                                                                
                  }                                                                                                     
                                                                                                                        
                  if ($inventory->stock <= 0) {                                                                         
                      return [                                                                                          
                          'code' => 400,                                                                                
                          'message' => '数据库库存不足',                                                                
                          'data' => null,                                                                               
                      ];                                                                                                
                  }                                                                                                     
                                                                                                                        
                  $inventory->stock -= 1;                                                                               
                  $inventory->save();                                                                                   
                                                                                                                        
                  return [                                                                                              
                      'code' => 0,                                                                                      
                      'message' => '秒杀成功',                                                                          
                      'data' => [                                                                                       
                          'product_id' => $productId,                                                                   
                          'user_id' => $userId,                                                                         
                          'left_db_stock' => $inventory->stock,                                                         
                          'order_no' => 'SK' . date('YmdHis') . mt_rand(1000, 9999),                                    
                      ],                                                                                                
                  ];                                                                                                    
              });                                                                                                       
                                                                                                                        
              if (($result['code'] ?? 500) !== 0) {                                                                     
                  $this->redisService->set($stockKey, $stock);                                                          
                  $this->redisService->delete($userOrderKey);                                                           
              }                                                                                                         
                                                                                                                        
              return $result;                                                                                           
          }, 5);                                                                                                        
      }                                                                                                                 
                                                                                                                        
      public function initStock(int $productId, int $stock): array                                                      
      {                                                                                                                 
          $this->redisService->set("seckill:stock:{$productId}", $stock);                                               
                                                                                                                        
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => '秒杀库存初始化成功',                                                                        
              'data' => [                                                                                               
                  'product_id' => $productId,                                                                           
                  'stock' => $stock,                                                                                    
              ],                                                                                                        
          ];                                                                                                            
      }                                                                                                                 
                                                                                                                        
      public function getStock(int $productId): array                                                                   
      {                                                                                                                 
          return [                                                                                                      
              'code' => 0,                                                                                              
              'message' => 'success',                                                                                   
              'data' => [                                                                                               
                  'product_id' => $productId,                                                                           
                  'stock' => (int) ($this->redisService->get("seckill:stock:{$productId}") ?? 0),                       
              ],                                                                                                        
          ];                                                                                                            
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 8.3 Controller                                                                                                     
                                                                                                                        
  文件：app/Controller/SeckillController.php                                                                            
                                                                                                                        
  <?php                                                                                                                 
                                                                                                                        
  declare(strict_types=1);                                                                                              
                                                                                                                        
  namespace App\Controller;                                                                                             
                                                                                                                        
  use App\Service\SeckillService;                                                                                       
  use Hyperf\HttpServer\Annotation\Controller;                                                                          
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                          
  use Hyperf\HttpServer\Annotation\PostMapping;                                                                         
                                                                                                                        
  #[Controller(prefix: 'seckill')]                                                                                      
  class SeckillController                                                                                               
  {                                                                                                                     
      public function __construct(                                                                                      
          protected SeckillService $seckillService                                                                      
      ) {                                                                                                               
      }                                                                                                                 
                                                                                                                        
      #[PostMapping('init')]                                                                                            
      public function init(): array                                                                                     
      {                                                                                                                 
          return $this->seckillService->initStock(1001, 20);                                                            
      }                                                                                                                 
                                                                                                                        
      #[GetMapping('stock')]                                                                                            
      public function stock(): array                                                                                    
      {                                                                                                                 
          return $this->seckillService->getStock(1001);                                                                 
      }                                                                                                                 
                                                                                                                        
      #[PostMapping('rush')]                                                                                            
      public function rush(): array                                                                                     
      {                                                                                                                 
          return $this->seckillService->rush(1001, 88);                                                                 
      }                                                                                                                 
  }                                                                                                                     
                                                                                                                        
  ## 8.4 原理                                                                                                           
                                                                                                                        
  - Redis 预减库存                                                                                                      
  - 用户抢购标记防重复下单                                                                                              
  - 商品维度锁防并发                                                                                                    
  - MySQL 事务做最终一致性                                                                                              
  - 数据库失败时回滚 Redis 状态                                                                                         
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 9. Redis 客户端查看数据                                                                                             
                                                                                                                        
  ## 9.1 命令行                                                                                                         
                                                                                                                        
  redis-cli                                                                                                             
                                                                                                                        
  ### 查看短信验证码                                                                                                    
                                                                                                                        
  keys sms:*                                                                                                            
  get sms:code:13800138000                                                                                              
  ttl sms:code:13800138000                                                                                              
                                                                                                                        
  ### 查看分布式锁                                                                                                      
                                                                                                                        
  keys lock:*                                                                                                           
  get lock:order:submit:1001                                                                                            
  ttl lock:order:submit:1001                                                                                            
                                                                                                                        
  ### 查看普通库存锁                                                                                                    
                                                                                                                        
  keys lock:inventory:*                                                                                                 
                                                                                                                        
  ### 查看秒杀数据                                                                                                      
                                                                                                                        
  keys seckill:*                                                                                                        
  get seckill:stock:1001                                                                                                
  get seckill:order:user:1001:88                                                                                        
  ttl seckill:order:user:1001:88                                                                                        
                                                                                                                        
  ## 9.2 图形化客户端                                                                                                   
                                                                                                                        
  可使用：                                                                                                              
                                                                                                                        
  - RedisInsight                                                                                                        
  - Another Redis Desktop Manager                                                                                       
  - Redis Desktop Manager                                                                                               
                                                                                                                        
  可以直接查看：                                                                                                        
                                                                                                                        
  - key                                                                                                                 
  - value                                                                                                               
  - ttl                                                                                                                 
  - db                                                                                                                  
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 10. 常见问题                                                                                                        
                                                                                                                        
  ## 10.1 看不到 key                                                                                                    
                                                                                                                        
  可能原因：                                                                                                            
                                                                                                                        
  - key 已经过期                                                                                                        
  - 连错 Redis 实例                                                                                                     
  - 看错 DB                                                                                                             
  - 锁时间太短                                                                                                          
                                                                                                                        
  ## 10.2 heredoc Lua 报错                                                                                              
                                                                                                                        
  正确写法：                                                                                                            
                                                                                                                        
  $lua = <<<LUA                                                                                                         
  if redis.call("get", KEYS[1]) == ARGV[1] then                                                                         
      return redis.call("del", KEYS[1])                                                                                 
  else                                                                                                                  
      return 0                                                                                                          
  end                                                                                                                   
  LUA;                                                                                                                  
                                                                                                                        
  注意：                                                                                                                
                                                                                                                        
  - LUA; 必须顶格                                                                                                       
  - 前面不能有空格                                                                                                      
                                                                                                                        
  ## 10.3 为什么锁必须有过期时间                                                                                        
                                                                                                                        
  防止程序异常后锁不释放，造成死锁。                                                                                    
                                                                                                                        
  ## 10.4 为什么释放锁不能直接 del                                                                                      
                                                                                                                        
  因为 get + del 不是原子操作，可能误删别人后来获得的锁。                                                               
                                                                                                                        
  ———                                                                                                                   
                                                                                                                        
  # 11. 总结                                                                                                            
                                                                                                                        
  本文档覆盖了 Hyperf + Redis 的核心实战场景：                                                                          
                                                                                                                        
  ## 基础能力                                                                                                           
                                                                                                                        
  - RedisService 封装                                                                                                   
  - Redis key 规范                                                                                                      
                                                                                                                        
  ## 业务能力                                                                                                           
                                                                                                                        
  - 短信验证码                                                                                                          
  - 登录失败次数限制                                                                                                    
  - 分布式锁                                                                                                            
  - 普通库存扣减                                                                                                        
  - 秒杀库存扣减                                                                                                        
                                                                                                                        
  建议生产项目按下面思路建设：
- RedisService 做统一封装                                                                                             
  - RedisKey 常量类统一 key 命名                                                                                        
  - 锁服务独立抽象                                                                                                      
  - 库存与秒杀场景同时使用 Redis + MySQL 兜底

## 1. 文档目标                                                                                                                                                                   
                                                                                                                                                                                   
  本文档包含三部分内容：                                                                                                                                                           
                                                                                                                                                                                   
  1. `RedisService` 企业级基础封装                                                                                                                                                 
  2. 基于 Redis 的短信验证码 Demo                                                                                                                                                  
  3. 基于 Redis 的分布式锁 Demo                                                                                                                                                    
                                                                                                                                                                                   
  同时包含：                                                                                                                                                                       
                                                                                                                                                                                   
  - 完整示例代码                                                                                                                                                                   
  - 关键代码注释                                                                                                                                                                   
  - 实现原理说明                                                                                                                                                                   
  - Redis 客户端如何查看数据                                                                                                                                                       
                                                                                                                                                                                   
  ---                                                                                                                                                                              
                                                                                                                                                                                   
  ## 2. 环境准备                                                                                                                                                                   
                                                                                                                                                                                   
  安装 Redis 组件：                                                                                                                                                                
                                                                                                                                                                                   
  ```bash                                                                                                                                                                          
  composer require hyperf/redis                                                                                                                                                    
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 3. Redis 配置                                                                                                                                                                 
                                                                                                                                                                                   
  文件路径：                                                                                                                                                                       
                                                                                                                                                                                   
  config/autoload/redis.php                                                                                                                                                        
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  return [                                                                                                                                                                         
      'default' => [                                                                                                                                                               
          // Redis 主机地址                                                                                                                                                        
          'host' => env('REDIS_HOST', '127.0.0.1'),                                                                                                                                
                                                                                                                                                                                   
          // Redis 密码，没有则为 null                                                                                                                                             
          'auth' => env('REDIS_AUTH', null),                                                                                                                                       
                                                                                                                                                                                   
          // Redis 端口                                                                                                                                                            
          'port' => (int) env('REDIS_PORT', 6379),                                                                                                                                 
                                                                                                                                                                                   
          // Redis 数据库编号                                                                                                                                                      
          'db' => (int) env('REDIS_DB', 0),                                                                                                                                        
                                                                                                                                                                                   
          // 连接池配置                                                                                                                                                            
          'pool' => [                                                                                                                                                              
              'min_connections' => 1,                                                                                                                                              
              'max_connections' => 20,                                                                                                                                             
              'connect_timeout' => 10.0,                                                                                                                                           
              'wait_timeout' => 3.0,                                                                                                                                               
              'heartbeat' => -1,                                                                                                                                                   
              'max_idle_time' => 60.0,                                                                                                                                             
          ],                                                                                                                                                                       
                                                                                                                                                                                   
          // Redis 选项                                                                                                                                                            
          'options' => [],                                                                                                                                                         
      ],                                                                                                                                                                           
  ];                                                                                                                                                                               
                                                                                                                                                                                   
  .env 示例：                                                                                                                                                                      
                                                                                                                                                                                   
  REDIS_HOST=127.0.0.1                                                                                                                                                             
  REDIS_PORT=6379                                                                                                                                                                  
  REDIS_AUTH=                                                                                                                                                                      
  REDIS_DB=0                                                                                                                                                                       
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 4. RedisService 企业封装                                                                                                                                                       
                                                                                                                                                                                   
  ## 4.1 作用                                                                                                                                                                      
                                                                                                                                                                                   
  RedisService 的目标是：                                                                                                                                                          
                                                                                                                                                                                   
  - 统一管理 set/get/delete                                                                                                                                                        
  - 封装 JSON 序列化和反序列化                                                                                                                                                     
  - 隐藏底层 Redis 调用细节                                                                                                                                                        
  - 给业务层提供更稳定的操作入口                                                                                                                                                   
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 4.2 示例代码                                                                                                                                                                  
                                                                                                                                                                                   
  文件路径：                                                                                                                                                                       
                                                                                                                                                                                   
  app/Service/RedisService.php                                                                                                                                                     
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Service;                                                                                                                                                           
                                                                                                                                                                                   
  use Hyperf\Di\Annotation\Inject;                                                                                                                                                 
  use Hyperf\Redis\Redis;                                                                                                                                                          
                                                                                                                                                                                   
  class RedisService                                                                                                                                                               
  {                                                                                                                                                                                
      #[Inject]                                                                                                                                                                    
      protected Redis $redis;                                                                                                                                                      
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 写入缓存                                                                                                                                                                  
       *                                                                                                                                                                           
       * @param string $key Redis key                                                                                                                                              
       * @param mixed $value 支持字符串、数字、数组等                                                                                                                              
       * @param int $ttl 过期时间，单位秒，0 表示不过期                                                                                                                            
       */                                                                                                                                                                          
      public function set(string $key, mixed $value, int $ttl = 0): bool                                                                                                           
      {                                                                                                                                                                            
          // 如果不是标量，则统一转为 JSON 字符串                                                                                                                                  
          $value = is_scalar($value)                                                                                                                                               
              ? (string) $value                                                                                                                                                    
              : json_encode($value, JSON_UNESCAPED_UNICODE);                                                                                                                       
                                                                                                                                                                                   
          // 有过期时间则带 TTL 写入                                                                                                                                               
          if ($ttl > 0) {                                                                                                                                                          
              return (bool) $this->redis->set($key, $value, $ttl);                                                                                                                 
          }                                                                                                                                                                        
                                                                                                                                                                                   
          // 无过期时间直接写入                                                                                                                                                    
          return (bool) $this->redis->set($key, $value);                                                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 读取缓存                                                                                                                                                                  
       *                                                                                                                                                                           
       * @param string $key Redis key                                                                                                                                              
       * @param bool $decodeJson 是否尝试按 JSON 解析                                                                                                                              
       */                                                                                                                                                                          
      public function get(string $key, bool $decodeJson = false): mixed                                                                                                            
      {                                                                                                                                                                            
          $value = $this->redis->get($key);                                                                                                                                        
                                                                                                                                                                                   
          // Redis 中不存在时返回 null                                                                                                                                             
          if ($value === false || $value === null) {                                                                                                                               
              return null;                                                                                                                                                         
          }                                                                                                                                                                        
                                                                                                                                                                                   
          // 如果业务要求返回数组，则进行 JSON 反序列化                                                                                                                            
          if ($decodeJson) {                                                                                                                                                       
              return json_decode($value, true);                                                                                                                                    
          }                                                                                                                                                                        
                                                                                                                                                                                   
          return $value;                                                                                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 删除 key                                                                                                                                                                  
       */                                                                                                                                                                          
      public function delete(string $key): int                                                                                                                                     
      {                                                                                                                                                                            
          return $this->redis->del($key);                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 判断 key 是否存在                                                                                                                                                         
       */                                                                                                                                                                          
      public function exists(string $key): bool                                                                                                                                    
      {                                                                                                                                                                            
          return (bool) $this->redis->exists($key);                                                                                                                                
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 设置过期时间                                                                                                                                                              
       */                                                                                                                                                                          
      public function expire(string $key, int $ttl): bool                                                                                                                          
      {                                                                                                                                                                            
          return (bool) $this->redis->expire($key, $ttl);                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 自增                                                                                                                                                                      
       */                                                                                                                                                                          
      public function incr(string $key, int $by = 1): int                                                                                                                          
      {                                                                                                                                                                            
          return $by === 1                                                                                                                                                         
              ? $this->redis->incr($key)                                                                                                                                           
              : $this->redis->incrBy($key, $by);                                                                                                                                   
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 获取剩余过期时间                                                                                                                                                          
       */                                                                                                                                                                          
      public function ttl(string $key): int                                                                                                                                        
      {                                                                                                                                                                            
          return $this->redis->ttl($key);                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 返回底层 Redis 客户端                                                                                                                                                     
       *                                                                                                                                                                           
       * 用于执行 Lua、事务等高级操作                                                                                                                                              
       */                                                                                                                                                                          
      public function getClient(): Redis                                                                                                                                           
      {                                                                                                                                                                            
          return $this->redis;                                                                                                                                                     
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 4.3 原理说明                                                                                                                                                                  
                                                                                                                                                                                   
  ### 1）为什么要封装一层 RedisService                                                                                                                                             
                                                                                                                                                                                   
  如果所有业务代码都直接写：                                                                                                                                                       
                                                                                                                                                                                   
  $this->redis->set(...)                                                                                                                                                           
  $this->redis->get(...)                                                                                                                                                           
                                                                                                                                                                                   
  后续会有问题：                                                                                                                                                                   
                                                                                                                                                                                   
  - 代码重复                                                                                                                                                                       
  - JSON 序列化规则不统一                                                                                                                                                          
  - key 命名不统一                                                                                                                                                                 
  - 修改 Redis 逻辑时不方便集中调整                                                                                                                                                
                                                                                                                                                                                   
  封装后，业务层只依赖：                                                                                                                                                           
                                                                                                                                                                                   
  RedisService                                                                                                                                                                     
                                                                                                                                                                                   
  这样更利于维护。                                                                                                                                                                 
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 2）为什么要统一处理 JSON                                                                                                                                                     
                                                                                                                                                                                   
  Redis 原生存的是字符串。                                                                                                                                                         
  如果直接写数组：                                                                                                                                                                 
                                                                                                                                                                                   
  ['name' => '张三']                                                                                                                                                               
                                                                                                                                                                                   
  必须先转换成字符串，否则无法直接存储。                                                                                                                                           
                                                                                                                                                                                   
  所以常见做法是：                                                                                                                                                                 
                                                                                                                                                                                   
  json_encode($value)                                                                                                                                                              
                                                                                                                                                                                   
  读取时再：                                                                                                                                                                       
                                                                                                                                                                                   
  json_decode($value, true)                                                                                                                                                        
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 5. 短信验证码 Demo                                                                                                                                                             
                                                                                                                                                                                   
  ## 5.1 业务目标                                                                                                                                                                  
                                                                                                                                                                                   
  实现两个功能：                                                                                                                                                                   
                                                                                                                                                                                   
  1. 发送验证码                                                                                                                                                                    
  2. 校验验证码                                                                                                                                                                    
                                                                                                                                                                                   
  同时支持：                                                                                                                                                                       
                                                                                                                                                                                   
  - 验证码 5 分钟有效                                                                                                                                                              
  - 60 秒内不能重复发送                                                                                                                                                            
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 5.2 Redis Key 设计                                                                                                                                                            
                                                                                                                                                                                   
  sms:code:13800138000   // 验证码内容，300 秒过期                                                                                                                                 
  sms:lock:13800138000   // 发送频率锁，60 秒过期                                                                                                                                  
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 5.3 Service 示例代码                                                                                                                                                          
                                                                                                                                                                                   
  文件路径：                                                                                                                                                                       
                                                                                                                                                                                   
  app/Service/SmsCodeService.php                                                                                                                                                   
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Service;                                                                                                                                                           
                                                                                                                                                                                   
  class SmsCodeService                                                                                                                                                             
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected RedisService $redisService                                                                                                                                     
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 发送验证码                                                                                                                                                                
       */                                                                                                                                                                          
      public function sendCode(string $mobile): array                                                                                                                              
      {                                                                                                                                                                            
          // 限制同一手机号 60 秒内不能重复发送                                                                                                                                    
          $sendLockKey = "sms:lock:{$mobile}";                                                                                                                                     
                                                                                                                                                                                   
          // 验证码存储 key                                                                                                                                                        
          $codeKey = "sms:code:{$mobile}";                                                                                                                                         
                                                                                                                                                                                   
          // 如果发送锁存在，说明发送过于频繁                                                                                                                                      
          if ($this->redisService->exists($sendLockKey)) {                                                                                                                         
              return [                                                                                                                                                             
                  'code' => 429,                                                                                                                                                   
                  'message' => '发送过于频繁，请稍后再试',                                                                                                                         
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          // 生成 6 位随机验证码                                                                                                                                                   
          $code = (string) random_int(100000, 999999);                                                                                                                             
                                                                                                                                                                                   
          // 验证码保存 300 秒                                                                                                                                                     
          $this->redisService->set($codeKey, $code, 300);                                                                                                                          
                                                                                                                                                                                   
          // 发送锁保存 60 秒                                                                                                                                                      
          $this->redisService->set($sendLockKey, 1, 60);                                                                                                                           
                                                                                                                                                                                   
          // 真实项目里这里通常会调用短信平台发送                                                                                                                                  
          return [                                                                                                                                                                 
              'code' => 0,                                                                                                                                                         
              'message' => '验证码发送成功',                                                                                                                                       
              'data' => [                                                                                                                                                          
                  'mobile' => $mobile,                                                                                                                                             
                  'code' => $code,                                                                                                                                                 
                  'expire' => 300,                                                                                                                                                 
              ],                                                                                                                                                                   
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 校验验证码                                                                                                                                                                
       */                                                                                                                                                                          
      public function verifyCode(string $mobile, string $code): array                                                                                                              
      {                                                                                                                                                                            
          $codeKey = "sms:code:{$mobile}";                                                                                                                                         
                                                                                                                                                                                   
          // 从 Redis 中读取验证码                                                                                                                                                 
          $cachedCode = $this->redisService->get($codeKey);                                                                                                                        
                                                                                                                                                                                   
          // key 不存在，说明验证码过期                                                                                                                                            
          if (! $cachedCode) {                                                                                                                                                     
              return [                                                                                                                                                             
                  'code' => 400,                                                                                                                                                   
                  'message' => '验证码已过期',                                                                                                                                     
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          // 不一致，说明验证码错误                                                                                                                                                
          if ($cachedCode !== $code) {                                                                                                                                             
              return [                                                                                                                                                             
                  'code' => 400,                                                                                                                                                   
                  'message' => '验证码错误',                                                                                                                                       
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          // 验证成功后删除，避免重复使用                                                                                                                                          
          $this->redisService->delete($codeKey);                                                                                                                                   
                                                                                                                                                                                   
          return [                                                                                                                                                                 
              'code' => 0,                                                                                                                                                         
              'message' => '验证码校验成功',                                                                                                                                       
              'data' => null,                                                                                                                                                      
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 5.4 Controller 示例代码                                                                                                                                                       
                                                                                                                                                                                   
  文件路径：                                                                                                                                                                       
                                                                                                                                                                                   
  app/Controller/SmsController.php                                                                                                                                                 
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Controller;                                                                                                                                                        
                                                                                                                                                                                   
  use App\Service\SmsCodeService;                                                                                                                                                  
  use Hyperf\HttpServer\Annotation\Controller;                                                                                                                                     
  use Hyperf\HttpServer\Annotation\GetMapping;                                                                                                                                     
                                                                                                                                                                                   
  #[Controller(prefix: 'sms')]                                                                                                                                                     
  class SmsController                                                                                                                                                              
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected SmsCodeService $smsCodeService                                                                                                                                 
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 发送验证码示例                                                                                                                                                            
       */                                                                                                                                                                          
      #[GetMapping('send')]                                                                                                                                                        
      public function send(): array                                                                                                                                                
      {                                                                                                                                                                            
          $mobile = '13800138000';                                                                                                                                                 
                                                                                                                                                                                   
          return $this->smsCodeService->sendCode($mobile);                                                                                                                         
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 验证验证码示例                                                                                                                                                            
       */                                                                                                                                                                          
      #[GetMapping('verify')]                                                                                                                                                      
      public function verify(): array                                                                                                                                              
      {                                                                                                                                                                            
          $mobile = '13800138000';                                                                                                                                                 
          $code = '123456';                                                                                                                                                        
                                                                                                                                                                                   
          return $this->smsCodeService->verifyCode($mobile, $code);                                                                                                                
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 5.5 原理说明                                                                                                                                                                  
                                                                                                                                                                                   
  ### 1）为什么要存两类 key                                                                                                                                                        
                                                                                                                                                                                   
  #### 验证码 key                                                                                                                                                                  
                                                                                                                                                                                   
  用于保存验证码本身，例如：                                                                                                                                                       
                                                                                                                                                                                   
  sms:code:13800138000 => 123456                                                                                                                                                   
                                                                                                                                                                                   
  #### 发送锁 key                                                                                                                                                                  
                                                                                                                                                                                   
  用于控制发送频率，例如：                                                                                                                                                         
                                                                                                                                                                                   
  sms:lock:13800138000 => 1                                                                                                                                                        
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 2）为什么验证码要删除                                                                                                                                                        
                                                                                                                                                                                   
  验证码成功校验后应立即删除，否则一个验证码可能被重复使用，存在安全风险。                                                                                                         
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 3）为什么验证码要设置过期时间                                                                                                                                                
                                                                                                                                                                                   
  验证码是短期凭证，通常有效期 3~5 分钟。                                                                                                                                          
  使用 Redis TTL 可以自动失效，不需要手工清理。                                                                                                                                    
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 5.6 Redis 客户端中能看到什么                                                                                                                                                  
                                                                                                                                                                                   
  发送验证码后，你可以在 Redis 客户端中看到：                                                                                                                                      
                                                                                                                                                                                   
  sms:code:13800138000                                                                                                                                                             
  sms:lock:13800138000                                                                                                                                                             
                                                                                                                                                                                   
  查看方式：                                                                                                                                                                       
                                                                                                                                                                                   
  keys sms:*                                                                                                                                                                       
  get sms:code:13800138000                                                                                                                                                         
  ttl sms:code:13800138000                                                                                                                                                         
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 6. 分布式锁 Demo                                                                                                                                                               
                                                                                                                                                                                   
  ## 6.1 业务目标                                                                                                                                                                  
                                                                                                                                                                                   
  用于防止重复提交，例如：                                                                                                                                                         
                                                                                                                                                                                   
  - 重复下单                                                                                                                                                                       
  - 重复支付                                                                                                                                                                       
  - 重复点击按钮                                                                                                                                                                   
  - 秒杀并发抢购                                                                                                                                                                   
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 6.2 锁 Key 设计                                                                                                                                                               
                                                                                                                                                                                   
  例如用户下单锁：                                                                                                                                                                 
                                                                                                                                                                                   
  lock:order:submit:1001                                                                                                                                                           
                                                                                                                                                                                   
  其中：                                                                                                                                                                           
                                                                                                                                                                                   
  - lock 表示锁前缀                                                                                                                                                                
  - order:submit 表示业务动作                                                                                                                                                      
  - 1001 表示用户 ID                                                                                                                                                               
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 6.3 Service 示例代码                                                                                                                                                          
                                                                                                                                                                                   
  文件路径：                                                                                                                                                                       
                                                                                                                                                                                   
  app/Service/RedisLockService.php                                                                                                                                                 
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Service;                                                                                                                                                           
                                                                                                                                                                                   
  use Hyperf\Redis\Redis;                                                                                                                                                          
                                                                                                                                                                                   
  class RedisLockService                                                                                                                                                           
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected RedisService $redisService                                                                                                                                     
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 获取锁                                                                                                                                                                    
       *                                                                                                                                                                           
       * @param string $key 锁 key                                                                                                                                                 
       * @param string $token 锁唯一标识                                                                                                                                           
       * @param int $ttl 锁过期时间                                                                                                                                                
       */                                                                                                                                                                          
      public function acquire(string $key, string $token, int $ttl = 10): bool                                                                                                     
      {                                                                                                                                                                            
          /** @var Redis $redis */                                                                                                                                                 
          $redis = $this->redisService->getClient();                                                                                                                               
                                                                                                                                                                                   
          // SET key value NX EX ttl                                                                                                                                               
          // NX: 只有 key 不存在时才设置成功                                                                                                                                       
          // EX: 设置过期时间，防止死锁                                                                                                                                            
          return (bool) $redis->set($key, $token, ['nx', 'ex' => $ttl]);                                                                                                           
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 释放锁                                                                                                                                                                    
       *                                                                                                                                                                           
       * 为什么要用 Lua：                                                                                                                                                          
       * - 先 get 再 del 不是原子操作                                                                                                                                              
       * - Lua 保证 Redis 内部一次性执行，防止误删他人锁                                                                                                                           
       */                                                                                                                                                                          
      public function release(string $key, string $token): bool                                                                                                                    
      {                                                                                                                                                                            
          $lua = <<<LUA                                                                                                                                                            
  if redis.call("get", KEYS[1]) == ARGV[1] then                                                                                                                                    
      return redis.call("del", KEYS[1])                                                                                                                                            
  else                                                                                                                                                                             
      return 0                                                                                                                                                                     
  end                                                                                                                                                                              
  LUA;                                                                                                                                                                             
                                                                                                                                                                                   
          $result = $this->redisService->getClient()->eval($lua, [$key, $token], 1);                                                                                               
                                                                                                                                                                                   
          return (int) $result === 1;                                                                                                                                              
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 包装成带锁执行                                                                                                                                                            
       *                                                                                                                                                                           
       * @param string $key 锁 key                                                                                                                                                 
       * @param callable $callback 业务逻辑                                                                                                                                        
       * @param int $ttl 锁超时时间                                                                                                                                                
       */                                                                                                                                                                          
      public function executeWithLock(string $key, callable $callback, int $ttl = 10): mixed                                                                                       
      {                                                                                                                                                                            
          // 每次加锁生成唯一 token，避免误删他人锁                                                                                                                                
          $token = uniqid('', true);                                                                                                                                               
                                                                                                                                                                                   
          // 加锁失败，直接返回错误                                                                                                                                                
          if (! $this->acquire($key, $token, $ttl)) {                                                                                                                              
              return [                                                                                                                                                             
                  'code' => 423,                                                                                                                                                   
                  'message' => '操作过于频繁，请稍后再试',                                                                                                                         
                  'data' => null,                                                                                                                                                  
              ];                                                                                                                                                                   
          }                                                                                                                                                                        
                                                                                                                                                                                   
          try {                                                                                                                                                                    
              // 成功获取锁，执行业务逻辑                                                                                                                                          
              return $callback();                                                                                                                                                  
          } finally {                                                                                                                                                              
              // 无论业务成功还是失败，都尝试释放锁                                                                                                                                
              $this->release($key, $token);                                                                                                                                        
          }                                                                                                                                                                        
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 6.4 Controller 示例代码                                                                                                                                                       
                                                                                                                                                                                   
  文件路径：                                                                                                                                                                       
                                                                                                                                                                                   
  app/Controller/OrderController.php                                                                                                                                               
                                                                                                                                                                                   
  <?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace App\Controller;                                                                                                                                                        
                                                                                                                                                                                   
  use App\Service\RedisLockService;                                                                                                                                                
  use Hyperf\HttpServer\Annotation\Controller;                                                                                                                                     
  use Hyperf\HttpServer\Annotation\PostMapping;                                                                                                                                    
                                                                                                                                                                                   
  #[Controller(prefix: 'order')]                                                                                                                                                   
  class OrderController                                                                                                                                                            
  {                                                                                                                                                                                
      public function __construct(                                                                                                                                                 
          protected RedisLockService $lockService                                                                                                                                  
      ) {                                                                                                                                                                          
      }                                                                                                                                                                            
                                                                                                                                                                                   
      /**                                                                                                                                                                          
       * 提交订单示例                                                                                                                                                              
       */                                                                                                                                                                          
      #[PostMapping('submit')]                                                                                                                                                     
      public function submit(): array                                                                                                                                              
      {                                                                                                                                                                            
          $userId = 1001;                                                                                                                                                          
                                                                                                                                                                                   
          // 为当前用户构造独立锁 key                                                                                                                                              
          $lockKey = "lock:order:submit:{$userId}";                                                                                                                                
                                                                                                                                                                                   
          $result = $this->lockService->executeWithLock($lockKey, function () use ($userId) {                                                                                      
              // 这里模拟真实业务逻辑，比如创建订单、扣库存等                                                                                                                      
              return [                                                                                                                                                             
                  'code' => 0,                                                                                                                                                     
                  'message' => '下单成功',                                                                                                                                         
                  'data' => [                                                                                                                                                      
                      'user_id' => $userId,                                                                                                                                        
                      'order_no' => 'ORD' . date('YmdHis'),                                                                                                                        
                  ],                                                                                                                                                               
              ];                                                                                                                                                                   
          }, 5);                                                                                                                                                                   
                                                                                                                                                                                   
          return is_array($result) ? $result : [                                                                                                                                   
              'code' => 0,                                                                                                                                                         
              'message' => 'success',                                                                                                                                              
              'data' => $result,                                                                                                                                                   
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }                                                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 6.5 原理说明                                                                                                                                                                  
                                                                                                                                                                                   
  ### 1）为什么需要分布式锁                                                                                                                                                        
                                                                                                                                                                                   
  在高并发场景下，同一个用户可能短时间内重复请求：                                                                                                                                 
                                                                                                                                                                                   
  - 连点两次“提交订单”                                                                                                                                                             
  - 接口重复重试                                                                                                                                                                   
  - 前端重复发请求                                                                                                                                                                 
                                                                                                                                                                                   
  如果不加锁，就可能出现：                                                                                                                                                         
                                                                                                                                                                                   
  - 重复下单                                                                                                                                                                       
  - 重复扣库存                                                                                                                                                                     
  - 重复支付                                                                                                                                                                       
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 2）为什么用 SET NX EX                                                                                                                                                        
                                                                                                                                                                                   
  Redis 分布式锁最经典的方式之一是：                                                                                                                                               
                                                                                                                                                                                   
  SET lock_key token NX EX 10                                                                                                                                                      
                                                                                                                                                                                   
  含义：                                                                                                                                                                           
                                                                                                                                                                                   
  - NX：key 不存在才允许设置，保证只有一个请求拿到锁                                                                                                                               
  - EX 10：10 秒后自动过期，防止程序异常导致锁无法释放                                                                                                                             
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 3）为什么释放锁不能直接 DEL                                                                                                                                                  
                                                                                                                                                                                   
  错误方式：                                                                                                                                                                       
                                                                                                                                                                                   
  if ($redis->get($key) === $token) {                                                                                                                                              
      $redis->del($key);                                                                                                                                                           
  }                                                                                                                                                                                
                                                                                                                                                                                   
  问题：                                                                                                                                                                           
                                                                                                                                                                                   
  - get 和 del 是两步                                                                                                                                                              
  - 如果中间锁过期并被别人重新拿到                                                                                                                                                 
  - 你再执行 del，就会把别人的锁删掉                                                                                                                                               
                                                                                                                                                                                   
  所以必须用 Lua 脚本保证原子性：                                                                                                                                                  
                                                                                                                                                                                   
  if redis.call("get", KEYS[1]) == ARGV[1] then                                                                                                                                    
      return redis.call("del", KEYS[1])                                                                                                                                            
  else                                                                                                                                                                             
      return 0                                                                                                                                                                     
  end                                                                                                                                                                              
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ### 4）为什么要用 token                                                                                                                                                          
                                                                                                                                                                                   
  加锁时，不能只写：                                                                                                                                                               
                                                                                                                                                                                   
  lock:order:submit:1001 => 1                                                                                                                                                      
                                                                                                                                                                                   
  因为释放锁时无法确认是不是“自己加的锁”。                                                                                                                                         
                                                                                                                                                                                   
  所以应写入唯一 token，例如：                                                                                                                                                     
                                                                                                                                                                                   
  lock:order:submit:1001 => 6812f7bc9a1f76.12345678                                                                                                                                
                                                                                                                                                                                   
  这样释放锁时就能校验归属。                                                                                                                                                       
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 6.6 Redis 客户端中能看到什么                                                                                                                                                  
                                                                                                                                                                                   
  请求下单接口后，锁持有期间你可以看到类似 key：                                                                                                                                   
                                                                                                                                                                                   
  lock:order:submit:1001                                                                                                                                                           
                                                                                                                                                                                   
  查看命令：                                                                                                                                                                       
                                                                                                                                                                                   
  keys lock:*                                                                                                                                                                      
  get lock:order:submit:1001                                                                                                                                                       
  ttl lock:order:submit:1001                                                                                                                                                       
                                                                                                                                                                                   
  注意：                                                                                                                                                                           
                                                                                                                                                                                   
  - 锁的 TTL 通常很短                                                                                                                                                              
  - 如果业务很快执行完，key 会很快被删除                                                                                                                                           
  - 可以临时把锁时间改大一些，方便观察                                                                                                                                             
                                                                                                                                                                                   
  例如：                                                                                                                                                                           
                                                                                                                                                                                   
  }, 30);                                                                                                                                                                          
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 7. Redis 客户端查看数据                                                                                                                                                        
                                                                                                                                                                                   
  ## 7.1 命令行查看                                                                                                                                                                
                                                                                                                                                                                   
  进入 Redis：                                                                                                                                                                     
                                                                                                                                                                                   
  redis-cli                                                                                                                                                                        
                                                                                                                                                                                   
  查看所有 key：                                                                                                                                                                   
                                                                                                                                                                                   
  keys *                                                                                                                                                                           
                                                                                                                                                                                   
  查看短信验证码相关：                                                                                                                                                             
                                                                                                                                                                                   
  keys sms:*                                                                                                                                                                       
  get sms:code:13800138000                                                                                                                                                         
  ttl sms:code:13800138000                                                                                                                                                         
                                                                                                                                                                                   
  查看分布式锁相关：                                                                                                                                                               
                                                                                                                                                                                   
  keys lock:*                                                                                                                                                                      
  get lock:order:submit:1001                                                                                                                                                       
  ttl lock:order:submit:1001                                                                                                                                                       
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 7.2 图形化客户端查看                                                                                                                                                          
                                                                                                                                                                                   
  常见工具：                                                                                                                                                                       
                                                                                                                                                                                   
  - RedisInsight                                                                                                                                                                   
  - Another Redis Desktop Manager                                                                                                                                                  
  - Redis Desktop Manager                                                                                                                                                          
                                                                                                                                                                                   
  可查看：                                                                                                                                                                         
                                                                                                                                                                                   
  - key 名称                                                                                                                                                                       
  - value 内容                                                                                                                                                                     
  - TTL                                                                                                                                                                            
  - 所属 DB                                                                                                                                                                        
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 8. 常见问题                                                                                                                                                                    
                                                                                                                                                                                   
  ## 8.1 为什么客户端看不到验证码或锁                                                                                                                                              
                                                                                                                                                                                   
  常见原因：                                                                                                                                                                       
                                                                                                                                                                                   
  1. key 已经过期                                                                                                                                                                  
  2. 连错 Redis 实例                                                                                                                                                               
  3. 看错 Redis DB                                                                                                                                                                 
  4. 锁时间太短，业务执行完就删掉了                                                                                                                                                
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 8.2 为什么锁必须带过期时间                                                                                                                                                    
                                                                                                                                                                                   
  如果业务异常退出，没有执行释放锁，锁会一直存在。                                                                                                                                 
  设置 TTL 可以避免死锁。                                                                                                                                                          
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  ## 8.3 为什么 heredoc Lua 容易报错                                                                                                                                               
                                                                                                                                                                                   
  RedisLockService 中 Lua 脚本常用 heredoc：                                                                                                                                       
                                                                                                                                                                                   
  $lua = <<<LUA                                                                                                                                                                    
  if redis.call("get", KEYS[1]) == ARGV[1] then                                                                                                                                    
      return redis.call("del", KEYS[1])                                                                                                                                            
  else                                                                                                                                                                             
      return 0                                                                                                                                                                     
  end                                                                                                                                                                              
  LUA;                                                                                                                                                                             
                                                                                                                                                                                   
  注意：                                                                                                                                                                           
                                                                                                                                                                                   
  - LUA; 必须顶格                                                                                                                                                                  
  - 前面不能有空格                                                                                                                                                                 
  - 否则可能出现语法错误                                                                                                                                                           
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 9. 推荐扩展方向                                                                                                                                                                
                                                                                                                                                                                   
  可以继续完善：                                                                                                                                                                   
                                                                                                                                                                                   
  1. 提取 Redis key 常量类                                                                                                                                                         
  2. 提取统一错误码                                                                                                                                                                
  3. 加入 FormRequest 参数校验                                                                                                                                                     
  4. 对接真实短信平台                                                                                                                                                              
  5. 订单锁中接入 MySQL 扣库存                                                                                                                                                     
  6. 加入登录验证码场景                                                                                                                                                            
  7. 把 RedisService 再拆分成 CacheService / LockService                                                                                                                           
                                                                                                                                                                                   
  ———                                                                                                                                                                              
                                                                                                                                                                                   
  # 10. 总结                                                                                                                                                                       
                                                                                                                                                                                   
  本文档实现了三类常用 Redis 场景：                                                                                                                                                
                                                                                                                                                                                   
  ## RedisService                                                                                                                                                                  
                                                                                                                                                                                   
  用于统一封装 Redis 基础操作。                                                                                                                                                    
                                                                                                                                                                                   
  ## 短信验证码                                                                                                                                                                    
                                                                                                                                                                                   
  用于：                                                                                                                                                                           
                                                                                                                                                                                   
  - 登录验证码                                                                                                                                                                     
  - 注册验证码                                                                                                                                                                     
  - 找回密码验证码                                                                                                                                                                 
                                                                                                                                                                                   
  核心是：                                                                                                                                                                         
                                                                                                                                                                                   
  - 校验成功后删除                                                                                                                                                                 
                                                                                                                                                                                   
  ## 分布式锁                                                                                                                                                                      
                                                                                                                                                                                   
  用于：                                                                                                                                                                           
                                                                                                                                                                                   
  - 防止重复下单                                                                                                                                                                   
  - 防止重复支付                                                                                                                                                                   
  - 防止接口重复提交                                                                                                                                                               
                                                                                                                                                                                   
  核心是：                                                                                                                                                                         
                                                                                                                                                                                   
  - SET NX EX                                                                                                                                                                      
  - 唯一 token                                                                                                                                                                     
  - Lua 原子释放锁