<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace 
{
    /**
     * models
     *
     * @author      Roman Telychko
     * @version     0.1.20130712
     */
    class models extends \db
    {
        /////////////////////////////////////////////////////////////////////////////

        protected   $_filters                       = false;
        protected   $_items                         = false;
        protected   $_properties                    = false;
        protected   $_orders                        = false;
        protected   $_pages                         = false;
        protected   $_news                          = false;
        protected   $_customers                     = false;
        protected   $_catalog                       = false;
        protected   $_admins                        = false;
        protected   $_partners                      = false;
        protected   $_callback                      = false;

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getFilters
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140321
         *
         * @return    	obj
         */
        public function getFilters()
        {
            if( empty($this->_language) )
            {
                $this->_filters = new \models\filters();
                $this->_filters->setDi( $this->getDi() );
            }
            
            return $this->_filters;
        }
        
        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getItems
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140327
         *
         * @return    	obj
         */
        public function getItems()
        {
            if( empty($this->_items) )
            {
                $this->_items = new \models\items();
                $this->_items->setDi( $this->getDi() );
            }

            return $this->_items;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getProperties
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140404
         *
         * @return    	obj
         */
        public function getProperties()
        {
            if( empty($this->_properties) )
            {
                $this->_properties = new \models\properties();
                $this->_properties->setDi( $this->getDi() );
            }

            return $this->_properties;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getOrders
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140422
         *
         * @return    	obj
         */
        public function getOrders()
        {
            if( empty($this->_orders) )
            {
                $this->_orders = new \models\orders();
                $this->_orders->setDi( $this->getDi() );
            }

            return $this->_orders;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getPages
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140423
         *
         * @return    	obj
         */
        public function getPages()
        {
            if( empty($this->_pages) )
            {
                $this->_pages = new \models\pages();
                $this->_pages->setDi( $this->getDi() );
            }

            return $this->_pages;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getNews
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140423
         *
         * @return    	obj
         */
        public function getNews()
        {
            if( empty($this->_news) )
            {
                $this->_news = new \models\news();
                $this->_news->setDi( $this->getDi() );
            }

            return $this->_news;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getCustomers
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140425
         *
         * @return    	obj
         */
        public function getCustomers()
        {
            if( empty($this->_customers) )
            {
                $this->_customers = new \models\customers();
                $this->_customers->setDi( $this->getDi() );
            }

            return $this->_customers;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getCatalog
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140428
         *
         * @return    	obj
         */
        public function getCatalog()
        {
            if( empty($this->_catalog) )
            {
                $this->_catalog = new \models\catalog();
                $this->_catalog->setDi( $this->getDi() );
            }

            return $this->_catalog;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getAdmins
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140505
         *
         * @return    	obj
         */
        public function getAdmins()
        {
            if( empty($this->_admins) )
            {
                $this->_admins = new \models\admins();
                $this->_admins->setDi( $this->getDi() );
            }

            return $this->_admins;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getPartners
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140506
         *
         * @return    	obj
         */
        public function getPartners()
        {
            if( empty($this->_partners) )
            {
                $this->_partners = new \models\partners();
                $this->_partners->setDi( $this->getDi() );
            }

            return $this->_partners;
        }

        /////////////////////////////////////////////////////////////////////////////

        /**
         * models::getCallback
         *
         * @author      Jane Bezmaternykh
         * @version     0.1.20140507
         *
         * @return    	obj
         */
        public function getCallback()
        {
            if( empty($this->_callback) )
            {
                $this->_callback = new \models\callback();
                $this->_callback->setDi( $this->getDi() );
            }

            return $this->_callback;
        }

        /////////////////////////////////////////////////////////////////////////////
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
