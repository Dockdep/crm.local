<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Prints human-readable information about a variable
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     5.0.20130409
     *
     *  color:
     *      0 | 1   -- die(), color => 2 (specific for variable type)
     *      2       -- specific for variable type
     *      3-10    -- other colors
     *      8       -- black    | transparent
     *      9       -- white    | red
     *      11      -- HTML-comment print
     *      12      -- print without formatting
     *
     *  for stop/die after show $var append to function name "~" or "!"
     *  example:
     *      !p( 'Hello, world!' );
     *
     * @param       string|array|object|boolen      $var
     * @param       integer                         $color      (if 0 or 1 -- print&die, else -- print)
     * @param       bool                            $show_input_var
     * @return      string/html
     */
    function p( $var, $color = 2, $show_input_var = false )
    {
        new p( $var, $color, $show_input_var );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Prints human-readable information about a variable for javascript files
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     5.0.20130409
     *
     * @param       string|array|object|boolen      $var
     * @return      text
     *
     */
    function j( $var )
    {
        new p( $var, 2, false, true );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Prints variable in JSON format
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     5.0.20131031
     *
     * @param       string|array|object|boolen      $var
     * @return      text
     *
     */
    function j2( $var )
    {
        echo(
            '<pre style="border-radius:2px;font:normal 12px/14px monospace;padding:4px;margin:2px 0 0 0;clear:both;display:block;">'.
                json_encode( $var, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE ).
            '</pre>'
        );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    class p
    {
        private $var                = false;
        private $color              = 2;
        private $show_input_var     = false;
        private $javascript_output  = false;

        private $input_var_styles   = 'border-radius:2px;color:#333333;font:normal 12px/14px Consolas,Monaco,Andale Mono,Courier New,monospace;padding:4px 8px;margin:2px 0 0 0;clear:both;display:block;background: #e0e0e0;background: -moz-linear-gradient(top, #e0e0e0 0%, #eeeeee 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e0e0e0), color-stop(100%,#eeeeee));background: -webkit-linear-gradient(top, #e0e0e0 0%,#eeeeee 100%);background: -o-linear-gradient(top, #e0e0e0 0%,#eeeeee 100%);background: -ms-linear-gradient(top, #e0e0e0 0%,#eeeeee 100%);background: linear-gradient(to bottom, #e0e0e0 0%,#eeeeee 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#e0e0e0\', endColorstr=\'#eeeeee\',GradientType=0 );';
        private $var_content_styles = 'border-radius:2px;font:normal 12px/14px monospace;padding:4px;margin:2px 0 0 0;clear:both;display:block;';

        private $output             = '';
        private $options            = array();

        public function __construct( $var, $color, $show_input_var, $javascript_output = false )
        {
            if( $color==0 || $color==1 )
            {
                $this->options['stop'] = true;
                $color = 2;
            }

            if( PHP_SAPI=='cli' || $color==12 )
            {
                $this->options['cli'] = true;
            }

            $this->var                  = $var;
            $this->color                = $color;
            $this->show_input_var       = $show_input_var;
            $this->javascript_output    = $javascript_output;

            if( $this->show_input_var )
            {
                $this->getInputVars();
            }

            $this->getVarContent()->outputVar();
        }

        protected function getInputVars()
        {
            $input_var = '';

            list( ,, $caller, ) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

            if( !empty($caller) && isset($caller['file']) && isset($caller['line']) && isset($caller['function']) )
            {
                if( $code = file( $caller['file'] ) )
                {
                    if( isset($code[ $caller['line']-1 ]) )
                    {
                        $line = trim( $code[ $caller['line']-1 ] );

                        unset($code);

                        $tokens = token_get_all( '<?php '.$line );

                        $start_gather_input = false;

                        foreach( $tokens as $token )
                        {
                            $skip = false;

                            if( is_string($token) )
                            {
                                if( $token=='~' || $token=='!' )
                                {
                                    if( !$start_gather_input )
                                    {
                                        $this->options['stop'] = true;
                                    }
                                }
                                elseif( $token=='(' )
                                {
                                    $start_gather_input = true;
                                    $skip = true;
                                }
                                elseif( $token==')' )
                                {
                                    break;
                                }
                            }

                            if( $start_gather_input && $skip===false )
                            {
                                $input_var .= is_string($token) ? $token : $token['1'];
                            }
                        }
                    }
                }

                $input_var = '> '.trim( $input_var );

                if( isset($this->options['cli']) && $this->options['cli'] )
                {
                    $this->output = $input_var;
                }
                elseif( $this->javascript_output )
                {
                    $this->output = $input_var;
                }
                else
                {
                    $this->output = '<div style="'.$this->input_var_styles.'">'.$input_var.'</div>';
                }
            }

            return false;
        }

        protected function getVarContent()
        {
            ob_start();

            // ARRAY ///////////////////////////////////////////////////////////////////////////////////////////////////

            if( is_array($this->var) )
            {
                print_r($this->var);

                $color = array(
                    'fg' => 'color:#ffffff;',
                    'ts' => '',//'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                    'bg' => "background:rgb(244,147,95);background:-moz-linear-gradient(top,rgba(244,147,95,1) 0%,rgba(244,127,63,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(244,147,95,1)), color-stop(100%,rgba(244,127,63,1)));background:-webkit-linear-gradient(top,rgba(244,147,95,1) 0%,rgba(244,127,63,1) 100%);background:-o-linear-gradient(top,rgba(244,147,95,1) 0%,rgba(244,127,63,1) 100%);background:-ms-linear-gradient(top,rgba(244,147,95,1) 0%,rgba(244,127,63,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f4935f', endColorstr='#f47f3f',GradientType=0 );background:linear-gradient(top,rgba(244,147,95,1) 0%,rgba(244,127,63,1) 100%);",
                );
            }

            // OBJECT //////////////////////////////////////////////////////////////////////////////////////////////////

            elseif( is_object($this->var) )
            {
                echo(
                    isset($this->options['cli']) && $this->options['cli']
                    ? 'object vars: '
                    : '<strong>object vars</strong>: '
                );
                print_r( get_object_vars($this->var) );

                echo(
                    "\n".
                    (
                        isset($this->options['cli']) && $this->options['cli']
                        ? 'class name: '
                        : '<strong>class name</strong>: '
                    )
                );
                echo( get_class($this->var) );

                echo(
                    "\n\n".
                    (
                        isset($this->options['cli']) && $this->options['cli']
                        ? 'class methods: '
                        : '<strong>class methods</strong>: '
                    )
                );
                print_r( get_class_methods($this->var) );

                echo(
                    "\n".
                    (
                        isset($this->options['cli']) && $this->options['cli']
                        ? 'class vars: '
                        : '<strong>class vars</strong>: '
                    )
                );
                print_r( get_class_vars(get_class($this->var)) );

                $color = array(
                    'fg' => 'color:#ffffff;',
                    'ts' => '',//'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                    'bg' => "background:rgb(67,137,193);background:-moz-linear-gradient(top,rgba(67,137,193,1) 0%,rgba(58,130,193,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(67,137,193,1)), color-stop(100%,rgba(58,130,193,1)));background:-webkit-linear-gradient(top,rgba(67,137,193,1) 0%,rgba(58,130,193,1) 100%);background:-o-linear-gradient(top,rgba(67,137,193,1) 0%,rgba(58,130,193,1) 100%);background:-ms-linear-gradient(top,rgba(67,137,193,1) 0%,rgba(58,130,193,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#4389c1', endColorstr='#3a82c1',GradientType=0 );background:linear-gradient(top,rgba(67,137,193,1) 0%,rgba(58,130,193,1) 100%);",
                );
            }

            // BOOL ////////////////////////////////////////////////////////////////////////////////////////////////////

            elseif( is_bool($this->var) )
            {
                echo( ($this->var==true) ? '[True]' : '[False]' );

                $color = array(
                    'fg' => 'color:#ffffff;',
                    'ts' => '',//'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                    'bg' => "background:rgb(152,190,222);background:-moz-linear-gradient(top,rgba(152,190,222,1) 0%,rgba(141,181,214,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(152,190,222,1)), color-stop(100%,rgba(141,181,214,1)));background:-webkit-linear-gradient(top,rgba(152,190,222,1) 0%,rgba(141,181,214,1) 100%);background:-o-linear-gradient(top,rgba(152,190,222,1) 0%,rgba(141,181,214,1) 100%);background:-ms-linear-gradient(top,rgba(152,190,222,1) 0%,rgba(141,181,214,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#98bede', endColorstr='#8db5d6',GradientType=0 );background:linear-gradient(top,rgba(152,190,222,1) 0%,rgba(141,181,214,1) 100%);"
                );
            }

            // INTEGER /////////////////////////////////////////////////////////////////////////////////////////////////

            elseif( is_int($this->var) )
            {
                echo( $this->var );

                $color = array(
                    'fg' => 'color:#ffffff;',
                    'ts' => '',//'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                    'bg' => "background:rgb(69,168,161);background:-moz-linear-gradient(top,rgba(69,168,161,1) 0%,rgba(13,173,162,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(69,168,161,1)), color-stop(100%,rgba(13,173,162,1)));background:-webkit-linear-gradient(top,rgba(69,168,161,1) 0%,rgba(13,173,162,1) 100%);background:-o-linear-gradient(top,rgba(69,168,161,1) 0%,rgba(13,173,162,1) 100%);background:-ms-linear-gradient(top,rgba(69,168,161,1) 0%,rgba(13,173,162,1) 100%)filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#45a8a1', endColorstr='#0dada2',GradientType=0 );background:linear-gradient(top,rgba(69,168,161,1) 0%,rgba(13,173,162,1) 100%);",
                );
            }

            // STRING/ELSE /////////////////////////////////////////////////////////////////////////////////////////////

            else
            {
                echo( empty($this->var) ? '[Empty]' : $this->var );

                $color = array(
                    'fg' => 'color:#ffffff;',
                    'ts' => '',//'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                    'bg' => "background:rgb(72,175,85);background:-moz-linear-gradient(top,rgba(72,175,85,1) 0%,rgba(34,173,53,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(72,175,85,1)), color-stop(100%,rgba(34,173,53,1)));background:-webkit-linear-gradient(top,rgba(72,175,85,1) 0%,rgba(34,173,53,1) 100%);background:-o-linear-gradient(top,rgba(72,175,85,1) 0%,rgba(34,173,53,1) 100%);background:-ms-linear-gradient(top,rgba(72,175,85,1) 0%,rgba(34,173,53,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#48af55', endColorstr='#22ad35',GradientType=0 );background:linear-gradient(top,rgba(72,175,85,1) 0%,rgba(34,173,53,1) 100%);",
                );
            }

            // COLOR ///////////////////////////////////////////////////////////////////////////////////////////////////
            if( $this->color > 2 )
            {
                switch($this->color)
                {
                    case 3:
                        $color = array(
                            'fg' => 'color:#000000;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);',
                            'bg' => "text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8); background:rgb(211,211,211);background:-moz-linear-gradient(top,rgba(211,211,211,1) 0%,rgba(204,204,204,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(211,211,211,1)), color-stop(100%,rgba(204,204,204,1)));background:-webkit-linear-gradient(top,rgba(211,211,211,1) 0%,rgba(204,204,204,1) 100%);background:-o-linear-gradient(top,rgba(211,211,211,1) 0%,rgba(204,204,204,1) 100%);background:-ms-linear-gradient(top,rgba(211,211,211,1) 0%,rgba(204,204,204,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d3d3d3', endColorstr='#cccccc',GradientType=0 );background:linear-gradient(top,rgba(211,211,211,1) 0%,rgba(204,204,204,1) 100%);"
                        );
                        break;
                    case 4:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(226,143,65);background:-moz-linear-gradient(top,rgba(226,143,65,1) 0%,rgba(226,129,38,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(226,143,65,1)), color-stop(100%,rgba(226,129,38,1)));background:-webkit-linear-gradient(top,rgba(226,143,65,1) 0%,rgba(226,129,38,1) 100%);background:-o-linear-gradient(top,rgba(226,143,65,1) 0%,rgba(226,129,38,1) 100%);background:-ms-linear-gradient(top,rgba(226,143,65,1) 0%,rgba(226,129,38,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e28f41', endColorstr='#e28126',GradientType=0 );background:linear-gradient(top,rgba(226,143,65,1) 0%,rgba(226,129,38,1) 100%);"
                        );
                        break;
                    case 5:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(112,165,87);background:-moz-linear-gradient(top,rgba(112,165,87,1) 0%,rgba(101,165,71,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(112,165,87,1)), color-stop(100%,rgba(101,165,71,1)));background:-webkit-linear-gradient(top,rgba(112,165,87,1) 0%,rgba(101,165,71,1) 100%);background:-o-linear-gradient(top,rgba(112,165,87,1) 0%,rgba(101,165,71,1) 100%);background:-ms-linear-gradient(top,rgba(112,165,87,1) 0%,rgba(101,165,71,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#70a557', endColorstr='#65a547',GradientType=0 );background:linear-gradient(top,rgba(112,165,87,1) 0%,rgba(101,165,71,1) 100%);"
                        );
                        break;
                    case 6:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(178,206,255);background:-moz-linear-gradient(top,rgba(178,206,255,1) 0%,rgba(152,176,217,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(178,206,255,1)), color-stop(100%,rgba(152,176,217,1)));background:-webkit-linear-gradient(top,rgba(178,206,255,1) 0%,rgba(152,176,217,1) 100%);background:-o-linear-gradient(top,rgba(178,206,255,1) 0%,rgba(152,176,217,1) 100%);background:-ms-linear-gradient(top,rgba(178,206,255,1) 0%,rgba(152,176,217,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b2ceff', endColorstr='#98b0d9',GradientType=0 );background:linear-gradient(top,rgba(178,206,255,1) 0%,rgba(152,176,217,1) 100%);"
                        );
                        break;
                    case 7:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(148,133,193);background:-moz-linear-gradient(top,rgba(148,133,193,1) 0%,rgba(131,110,193,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(148,133,193,1)), color-stop(100%,rgba(131,110,193,1)));background:-webkit-linear-gradient(top,rgba(148,133,193,1) 0%,rgba(131,110,193,1) 100%);background:-o-linear-gradient(top,rgba(148,133,193,1) 0%,rgba(131,110,193,1) 100%);background:-ms-linear-gradient(top,rgba(148,133,193,1) 0%,rgba(131,110,193,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9485c1', endColorstr='#836ec1',GradientType=0 );background:linear-gradient(top,rgba(148,133,193,1) 0%,rgba(131,110,193,1) 100%);"
                        );
                        break;
                    case 8:
                        $color = array(
                            'fg' => 'color:#000000;',
                            'ts' => '',
                            'bg' => 'background:transparent;'
                        );
                        break;
                    case 9:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(153,45,45);background:-moz-linear-gradient(top,rgba(153,45,45,1) 0%,rgba(153,0,0,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(153,45,45,1)), color-stop(100%,rgba(153,0,0,1)));background:-webkit-linear-gradient(top,rgba(153,45,45,1) 0%,rgba(153,0,0,1) 100%);background:-o-linear-gradient(top,rgba(153,45,45,1) 0%,rgba(153,0,0,1) 100%);background:-ms-linear-gradient(top,rgba(153,45,45,1) 0%,rgba(153,0,0,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#992d2d', endColorstr='#990000',GradientType=0 );background:linear-gradient(top,rgba(153,45,45,1) 0%,rgba(153,0,0,1) 100%);"
                        );
                        break;
                    case 10:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(213,166,189);background:-moz-linear-gradient(top,rgba(213,166,189,1) 0%,rgba(209,152,178,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(213,166,189,1)), color-stop(100%,rgba(209,152,178,1)));background:-webkit-linear-gradient(top,rgba(213,166,189,1) 0%,rgba(209,152,178,1) 100%);background:-o-linear-gradient(top,rgba(213,166,189,1) 0%,rgba(209,152,178,1) 100%);background:-ms-linear-gradient(top,rgba(213,166,189,1) 0%,rgba(209,152,178,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d5a6bd', endColorstr='#d198b2',GradientType=0 );background:linear-gradient(top,rgba(213,166,189,1) 0%,rgba(209,152,178,1) 100%);"
                        );
                        break;
                    default:
                        $color = array(
                            'fg' => 'color:#ffffff;',
                            'ts' => 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);',
                            'bg' => "background:rgb(244,179,66);background:-moz-linear-gradient(top,rgba(244,179,66,1) 0%,rgba(247,150,33,1) 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(244,179,66,1)), color-stop(100%,rgba(247,150,33,1)));background:-webkit-linear-gradient(top,rgba(244,179,66,1) 0%,rgba(247,150,33,1) 100%);background:-o-linear-gradient(top,rgba(244,179,66,1) 0%,rgba(247,150,33,1) 100%);background:-ms-linear-gradient(top,rgba(244,179,66,1) 0%,rgba(247,150,33,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f4b342', endColorstr='#f79621',GradientType=0 );background:linear-gradient(top,rgba(244,179,66,1) 0%,rgba(247,150,33,1) 100%);"
                        );
                        break;
                }
            }

            $html = ob_get_clean();

            if( isset($this->options['cli']) && $this->options['cli'] )
            {
                $this->output .= $html;
            }
            elseif( $this->javascript_output )
            {
                if( strpos( $html, "\n" )===false )
                {
                    $this->output .= '// '.$html."\n";
                }
                else
                {
                    $this->output .= '/*'."\n".$html."*/\n";
                }
            }
            else
            {
                $this->output .= '<pre style="'.$color['fg'].$color['ts'].$color['bg'].$this->var_content_styles.'">'.$html.'</pre>';
            }

            return $this;
        }

        protected function outputVar()
        {
            echo( $this->output );

            if( isset($this->options['stop']) && $this->options['stop'] )
            {
                die();
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Prints human-readable information about a variable only if set GET-value
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     1.2.20110913
     *
     *  color:
     *      0 | 1   -- die(), color => 2 (specific for variable type)
     *      2       -- specific for variable type
     *      3-10    -- other colors
     *      8       -- black    | transparent
     *      9       -- white    | red
     *      11      -- HTML-comment print
     *
     * @param       string|array|object|boolen      $var
     * @param       integer                         $die_color      (if 0 or 1 -- print&die, else -- print)
     * @param       string                          $get_param
     * @return      text/html
     *
     * @see         p()
     */
    function z($var = '', $die_color = 2, $get_param = 'z')
    {
        ///////////////////////////////////////////////////////////////////////////

        if( isset($_GET[$get_param]) )
        {
            if( function_exists('p') )
            {
                p( $var, $die_color );
            }
            else
            {
                echo( $var."\n" );
            }
        }

        ///////////////////////////////////////////////////////////////////////////
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Benchmark profiler
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     3.0.20121210
     *
     * @param       string|integer  $label
     * @param       bool            $use_p
     * @param       integer         $p_color
     * @return      text/html
     *
     * @see         $__b
     * @see         p()
     */
    function b( $label = '1', $use_p = true, $p_color = 2 )
    {
        global $__b;

        if( empty($label) )
        {
            // START_TIME must be defined!
            $__b[$label] = START_TIME;
        }
        if( isset($__b[$label]) )
        {
            if( function_exists('p') && $use_p==true )
            {
                p( 'label: "'.$label.'", time: '.( round( (microtime(true)-$__b[$label])*1000, 4 ) ).' ms', $p_color );
            }
            else
            {
                echo( 'label: "'.$label.'", time: '.( round( (microtime(true)-$__b[$label])*1000, 4 ) ).' ms'."\n" );
            }

            unset($__b[$label]);
        }
        else
        {
            $__b[$label] = microtime(true);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Print information about memory usage & build time
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     1.0.20110513
     *
     * @param       bool         $return
     * @param       bool         $with_html
     * @return      text/html
     */
    function info( $return = false, $with_html = true )
    {
        if( $with_html==true )
        {
            $data =
                '<div style="opacity:0.85; position:fixed; filter:alpha(opacity=85); background-color:#DEDEDE; background:-moz-linear-gradient(-90deg, #E8E8E8, #CBCBCB) repeat scroll 0 0 #DEDEDE; color:#333333; font:normal 12px/12px Georgia,Verdana,Arial,sans-serif; letter-spacing:normal; text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8); margin:0; padding:5px; width:240px; bottom:0; right:0; text-align:center; z-index:99999;">'.
                    ( defined('START_TIME') ? '<span>time:&nbsp;'.round( (microtime(true)-START_TIME)*1000, 3 ).'&nbsp;ms</span> | ' : '' ).
                    '<span>memory:&nbsp;'.round( memory_get_peak_usage()/1024, 3 ).'&nbsp;KB</span>'.
                '</div>';
        }
        else
        {
            $data = 'memory: '.round( memory_get_peak_usage()/1024,3 ).' KB'.( defined('START_TIME') ? ' | time: '.round( (microtime(true)-START_TIME)*1000, 3 ).' ms' : '' );
        }

        if( $return==false )
        {
            echo( $data );
            return true;
        }

        return $data;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Fire-PHP print
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     1.0.20111021
     *
     *  type:
     *      0, 1    -- die(), type => 2
     *      2, 6    -- info
     *      3, 8    -- log
     *      4, 7    -- warning
     *      5, 9    -- error
     *
     * @param       string|array|object|boolen      $var
     * @param       integer                         $type
     * @param       string                          $label
     * @return      text/html
     *
     * @see         p()
     */
    function f( $var = '', $type = 2, $label = null )
    {
        if( class_exists('FirePHP') )
        {
            switch( $type )
            {
                /*
                case 0:
                case 1:
                case 2:
                case 6:
                    $type_int = FirePHP::INFO;
                    break;
                */
                case 3:
                case 8:
                    $type_int = FirePHP::LOG;
                    break;

                case 4:
                case 7:
                    $type_int = FirePHP::WARN;
                    break;

                case 5:
                case 9:
                    $type_int = FirePHP::ERROR;
                    break;

                default:
                    $type_int = FirePHP::INFO;
                    break;
            }

            call_user_func_array(
                array(
                    FirePHP::getInstance(true),
                    'fb'
                ),
                array(
                    $var,
                    $label,
                    $type_int
                )
            );
        }
        else
        {
            p( $var, $type );
        }

        if( $type==0 || $type==1 )
        {
            die();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * fpe
     *
     * @author      Roman Telychko [ roman@romantelychko.com, http://romantelychko.com/ ]
     * @version     1.0.20130201
     *
     * @param       string|array|object|boolen      $str
     * @param       integer                         $color
     * @param       bool                            $append_newline
     * @return      text/html
     *
     * @see         p()
     */
    function fpe( $str, $color = 2, $append_newline = true )
    {
        if( function_exists('f') )
        {
            f( $str, $color );
        }
        else if( function_exists('p') )
        {
            p( $str, $color );
        }
        else
        {
            echo( $str );
        }

        if( $append_newline )
        {
            echo( "\n" );
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
