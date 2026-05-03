<?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace LhhGh\HyperfSwaggerSaGenerator\Command;                                                                                                                                
                                                                                                                                                                                   
  use Hyperf\Command\Annotation\Command;                                                                                                                                           
  use Hyperf\Command\Command as HyperfCommand;                                                                                                                                     
  use LhhGh\HyperfSwaggerSaGenerator\Generator\SwaggerSaGenerator;                                                                                                                 
  use Symfony\Component\Console\Input\InputArgument;                                                                                                                               
  use Symfony\Component\Console\Input\InputOption;                                                                                                                                 
                                                                                                                                                                                   
  #[Command]                                                                                                                                                                       
  class GenSwaggerSaCommand extends HyperfCommand                                                                                                                                  
  {                                                                                                                                                                                
      public function __construct()                                                                                                                                                
      {                                                                                                                                                                            
          parent::__construct('gen:swagger-sa');                                                                                                                                   
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function configure(): void                                                                                                                                            
      {                                                                                                                                                                            
          $this->setDescription('生成 Hyperf Swagger SA 风格 Controller 模板');                                                                                                    
          $this->addArgument('name', InputArgument::REQUIRED, '资源名称，例如 User');                                                                                              
          $this->addOption('tag', null, InputOption::VALUE_OPTIONAL, 'Swagger 标签名');                                                                                            
          $this->addOption('prefix', null, InputOption::VALUE_OPTIONAL, '路由前缀');                                                                                               
          $this->addOption(                                                                                                                                                        
              'only',                                                                                                                                                              
              null,                                                                                                                                                                
              InputOption::VALUE_OPTIONAL,                                                                                                                                         
              '仅生成指定方法，逗号分隔：list,detail,create,update,delete'                                                                                                         
          );                                                                                                                                                                       
      }                                                                                                                                                                            
                                                                                                                                                                                   
      public function handle()                                                                                                                                                     
      {                                                                                                                                                                            
          $name = (string) $this->input->getArgument('name');                                                                                                                      
          $tag = $this->input->getOption('tag');                                                                                                                                   
          $prefix = $this->input->getOption('prefix');                                                                                                                             
          $only = (string) ($this->input->getOption('only') ?? '');                                                                                                                
          $onlyList = $only === '' ? [] : array_map('trim', explode(',', $only));                                                                                                  
                                                                                                                                                                                   
          $basePath = BASE_PATH ?? getcwd();                                                                                                                                       
                                                                                                                                                                                   
          $generator = new SwaggerSaGenerator(dirname(__DIR__, 2), $basePath);                                                                                                     
                                                                                                                                                                                   
          $file = $generator->generate(                                                                                                                                            
              $name,                                                                                                                                                               
              $tag ? (string) $tag : null,                                                                                                                                         
              $prefix ? (string) $prefix : null,                                                                                                                                   
              $onlyList                                                                                                                                                            
          );                                                                                                                                                                       
                                                                                                                                                                                   
          $this->line('生成成功：' . $file);                                                                                                                                       
      }                                                                                                                                                                            
  }