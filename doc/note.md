

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