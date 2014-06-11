<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * profiler Class
     *
     * @author          Roman Telychko
     * @version         1.0.20130926
     */
    class profiler extends \core
    {
        ///////////////////////////////////////////////////////////////////////

        protected       $stats      = array();

        ///////////////////////////////////////////////////////////////////////

        /**
         * profiler::setStatistics()
         *
         * @author          Roman Telychko
         * @version         1.0.20131112
         *
         * @param           array       $data
         * @return          bool
         */
        public function setStatistics( $data = [] )
        {
            if( !isset($data['type']) )
            {
                return false;
            }

            $this->stats[ $data['type'] ][] = $data;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * profiler::getStatistics()
         *
         * @author          Roman Telychko
         * @version         1.0.20131112
         *
         * @param           string          $type
         * @return          array
         */
        public function getStatistics( $type = 'sql' )
        {
            if( empty($this->stats) || !isset($this->stats[$type]) || empty($this->stats[$type]) )
            {
                return false;
            }

            return $this->stats[ $type ];
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * profiler::getInfoStatistics()
         *
         * @author          Roman Telychko
         * @version         1.0.20131112
         *
         * @return          array
         */
        public function getInfoStatistics()
        {
            return
            [
                'exec'      => ( defined('START_TIME') ? round( ( microtime(true) - START_TIME ) * 1000, 3 ) : 0 ),             // in ms
                'memory'    => round( memory_get_peak_usage()/1024, 3 ),                                                        // in KB
                'db'        =>
                [
                    'count'     => $this->getStatistics('sql') ? count( $this->getStatistics('sql') ) : 0,
                    'time'      => round( array_sum( $this->getDi()->get('common')->array_column( $this->getStatistics('sql'), 'time' ) ) * 1000, 3 ),   // in ms
                ]
            ];
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * profiler::getAllStatistics()
         *
         * @author          Roman Telychko
         * @version         1.0.20131112
         *
         * @return          array
         */
        public function getAllStatistics()
        {
            return $this->stats;
        }

        ///////////////////////////////////////////////////////////////////////
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
