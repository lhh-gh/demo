<?php                                                                                                                                                                            
                                                                                                                                                                                   
  declare(strict_types=1);                                                                                                                                                         
                                                                                                                                                                                   
  namespace LhhGh\HyperfSwaggerSaGenerator;                                                                                                                                        
                                                                                                                                                                                   
  use LhhGh\HyperfSwaggerSaGenerator\Command\GenSwaggerSaCommand;                                                                                                                  
                                                                                                                                                                                   
  class ConfigProvider                                                                                                                                                             
  {                                                                                                                                                                                
      public function __invoke(): array                                                                                                                                            
      {                                                                                                                                                                            
          return [                                                                                                                                                                 
              'commands' => [                                                                                                                                                      
                  GenSwaggerSaCommand::class,                                                                                                                                      
              ],                                                                                                                                                                   
          ];                                                                                                                                                                       
      }                                                                                                                                                                            
  }