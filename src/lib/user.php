<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * user Class
     *
     * @author          Roman Telychko
     * @version         1.0.20131030
     */
    class user extends \core
    {
        ///////////////////////////////////////////////////////////////////////

        protected       $id                     = 0;
        protected       $account_type           = 1;

        ///////////////////////////////////////////////////////////////////////

        public          $redis_db_info          = 2;
        public          $redis_db_chat          = 3;
        public          $redis_db_cam           = 4;
        public          $redis_db_needrelogin   = 13;
        public          $redis_db_session       = 14;
        public          $redis_db_online        = 15;

        ///////////////////////////////////////////////////////////////////////

        public          $session_lifetime       = 1209600;      // 2 weeks

        ///////////////////////////////////////////////////////////////////////

        protected       $_db                    = false;
        protected       $_redis                 = false;

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::init()
         *
         * @author          Roman Telychko
         * @version         1.0.20140127
         */
        public function init()
        {
            $this->_db          = $this->getDi()->get('db');
            $this->_redis       = $this->getDi()->get('redis');

            $user_id            = $this->getID();
            $user_account_type  = $this->getAccountType();

            if( !empty($user_id) && !empty($user_account_type) )
            {
                // need relogin?
                if( $this->_redis->has( 'user_'.$user_id.'_need_relogin', $this->redis_db_needrelogin ) )
                {
                    return $this->getDi()->get('response')->redirect([ 'for' => 'user_logout' ]);
                }

                // set user as "online" for 300 sec (15 min)
                $this->_redis->set( 'account_type_'.$user_account_type.'/user_'.$user_id.'_online', 1, 900, $this->redis_db_online );

                // set user as "has cam" for 300 sec (15 min)
                if( $this->getDi()->get('session')->get( 'has_cam', 0 ) )
                {
                    $this->_redis->set( 'account_type_'.$user_account_type.'/user_'.$user_id.'_has_cam', 1, 900, $this->redis_db_info );
                }

                // set user last activity date
                $this->_redis->set( 'user_'.$user_id.'_last_activity', time(), -1, $this->redis_db_info );
            }
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::getID()
         *
         * @author          Roman Telychko
         * @version         1.0.20131030
         *
         * @return          integer
         */
        public function getID()
        {
            $this->id = $this->getDi()->get('session')->get('id', 0);

            if( empty($this->id) )
            {
                $this->id  = 0;
            }

            return $this->id;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::getAccountType()
         *
         * @author          Roman Telychko
         * @version         1.0.20131030
         *
         * @return          integer
         */
        public function getAccountType()
        {
            $this->account_type = $this->getDi()->get('session')->get('account_type');

            if( empty($this->account_type) )
            {
                $this->account_type  = 1;
            }

            return $this->account_type;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::getInfoByIDs()
         *
         * @author          Roman Telychko
         * @version         1.0.20140201
         *
         * @param           array           $ids
         * @return          array
         */
        public function getInfoByIDs( $ids = [] )
        {
            if( !is_array($ids) )
            {
                $ids = [ $ids ];
            }

            $data = [];

            if( empty($ids) )
            {
                return $data;
            }

            // only when ID > 0
            $ids_ = [];
            foreach( $ids as $id )
            {
                if( !empty($id) )
                {
                    $ids_[] = $id;
                }
            }

            // sort & unique
            $ids = array_unique( $ids_ );
            sort($ids);

            // get info from redis
            $data_redis_keys = [];

            foreach( $ids as $id )
            {
                $data_redis_keys[] = 'user_'.$id.'_info';
            }

            $data_redis = $this->_redis->mget( $data_redis_keys, $this->redis_db_info );

            if( !empty($data_redis) )
            {
                foreach( $data_redis as $d )
                {
                    if( !empty($d) )
                    {
                        $data[ $d['id'] ] = $d;
                    }
                }

                // create IDs to get from Database (exclude existent in Redis data)
                $data_db_ids = array_diff( $ids, array_keys($data) );
            }
            else
            {
                // create IDs to get from Database
                $data_db_ids = $ids;
            }

            // get info from db
            if( !empty($data_db_ids) )
            {
                $data_db = $this->getDi()->get('models')->getUser()->getUserInfo( $data_db_ids );

                if( empty($data_db) )
                {
                    return $data;
                }

                foreach( $data_db as &$d )
                {
                    // age
                    $d['age'] = intval( ( time() - strtotime($d['birth_date']) ) / 31556926 );             // 31556926 - year in seconds

                    // cache to redis
                    $this->_redis->set( 'user_'.$d['id'].'_info', $d, 3600, $this->redis_db_info );

                    // append to $data array
                    $data[ $d['id'] ] = $d;
                }
            }

            // update "online", "has_cam", etc.
            foreach( $data as &$d )
            {
                // is online
                $d['online']    = $this->_redis->has( 'account_type_'.$d['account_type'].'/user_'.$d['id'].'_online', $this->redis_db_online ) ? true : false;

                // has camera
                $d['has_cam']   = $this->_redis->has( 'account_type_'.$d['account_type'].'/user_'.$d['id'].'_has_cam', $this->redis_db_info ) ? true : false;

                // last activity timestamp ("was online")
                $d['last_activity'] = $this->_redis->get( 'user_'.$d['id'].'_last_activity', strtotime($d['lastlogin_date']), $this->redis_db_info );
            }

            return $data;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::dropCache()
         *
         * @author          Roman Telychko
         * @version         1.0.20131030
         *
         * @param           integer|array           $ids
         * @return          bool
         */
        public function dropCache( $ids = null )
        {
            if( empty($ids) )
            {
                $ids = $this->getID();
            }

            if( is_int($ids) || is_string($ids) )
            {
                $this->_redis->delete( 'user_'.$ids.'_info', $this->redis_db_info );

                return true;
            }
            elseif( is_array($ids) )
            {
                foreach( $ids as $id )
                {
                    $this->_redis->delete( 'user_'.$id.'_info', $this->redis_db_info );
                }

                return true;
            }

            return false;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::getProfileURL()
         *
         * @author          Roman Telychko
         * @version         1.0.20131031
         *
         * @param           integer             $id
         * @param           integer             $account_type
         * @return          string
         */
        public function getProfileURL( $id, $account_type = 2 )
        {
            switch( $account_type )
            {
                case 1:
                    $gender = 'm';
                    break;

                case 2:
                default:
                    $gender = 'w';
                    break;
            }

            return $this->getDi()->get('url')->get([ 'for' => 'get_profile', 'id' => $id, 'gender' => $gender ]);
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::getOnlineUsers()
         *
         * @author          Roman Telychko
         * @version         1.0.20131108
         *
         * @param           string|integer      $account_type
         * @return          array
         */
        public function getOnlineUsers( $account_type = '' )
        {
            $keys = $this->_redis->keys( 'account_type_'.$account_type.'*', $this->redis_db_online );

            $ids = [];

            if( !empty($keys) )
            {
                foreach( $keys as $key )
                {
                    if( preg_match( '#^account_type_[0-9]+/user_(?P<id>[0-9]+)_online$#', $key, $match ) )
                    {
                        if( isset($match['id']) && !empty($match['id']) )
                        {
                            $ids[] = $match['id'];
                        }
                    }
                }

                $ids = array_unique($ids);
                sort($ids);
            }

            return $ids;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::getUsersWithCam()
         *
         * @author          Jane Bezmaternykh
         * @version         1.0.20140129
         *
         * @param           string|integer      $account_type
         * @return          array
         */
        public function getUsersWithCam( $account_type = '' )
        {
            $keys = $this->_redis->keys( 'account_type_'.$account_type.'*', $this->redis_db_info );

            $ids = [];

            if( !empty($keys) )
            {
                foreach( $keys as $key )
                {
                    if( preg_match( '#^account_type_[0-9]+/user_(?P<id>[0-9]+)_has_cam$#', $key, $match ) )
                    {
                        if( isset($match['id']) && !empty($match['id']) )
                        {
                            $ids[] = $match['id'];
                        }
                    }
                }

                $ids = array_unique($ids);
                sort($ids);
            }

            return $ids;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::setNewChatMessages()
         *
         * @author          Roman Telychko
         * @version         1.0.20131226
         *
         * @param           integer             $receiver_id
         * @return          bool
         */
        public function setNewChatMessages( $receiver_id )
        {
            $key = 'user_'.$receiver_id.'_chat_'.$this->getID();

            if( $this->_redis->has( $key, $this->redis_db_chat ) )
            {
                // increment new (unread) messages count
                $this->_redis->inc( $key, $this->redis_db_chat );
            }
            else
            {
                // set new (unread) messages count
                $this->_redis->set( $key, 1, 86400, $this->redis_db_chat );
            }

            return true;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::hasNewChatMessages()
         *
         * @author          Roman Telychko
         * @version         1.0.20131226
         *
         * @param           integer             $user_id
         * @return          array
         */
        public function hasNewChatMessages( $user_id = null )
        {
            if( empty($user_id) )
            {
                $user_id = $this->getID();
            }

            $keys = $this->_redis->keys( 'user_'.$user_id.'_chat_*', $this->redis_db_chat );

            if( !empty($keys) )
            {
                $data = [];

                $keys_data = $this->_redis->mget( $keys, $this->redis_db_chat );

                if( !empty($keys_data) )
                {
                    foreach( $keys_data as $key => $messages_count )
                    {
                        if( preg_match( '#user_[0-9]+_chat_(?P<user_id>[0-9]+)#i', $key, $temp ) )
                        {
                            $data[ $temp['user_id'] ] = $messages_count;
                        }
                    }

                    return $data;
                }
            }

            return [];
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::clearNewChatMessages()
         *
         * @author          Roman Telychko
         * @version         1.0.20131226
         *
         * @param           integer             $user_id
         * @param           integer             $receiver_id
         * @return          bool
         */
        public function clearNewChatMessages( $user_id = null, $receiver_id )
        {
            if( empty($user_id) )
            {
                $user_id = $this->getID();
            }

            // delete new (unread) messages count
            if( $this->_redis->delete( 'user_'.$user_id.'_chat_'.$receiver_id, $this->redis_db_chat ) )
            {
                // drop recent activity for (receiver) user
                $this->_db->dropCache( 'user_activity_history_1_'.$receiver_id );

                return true;
            }

            return false;
        }

        ///////////////////////////////////////////////////////////////////////

        /**
         * user::reloginUser()
         *
         * @author          Roman Telychko
         * @version         1.0.20140127
         *
         * @param           integer             $user_id
         * @return          bool
         */
        public function reloginUser( $user_id )
        {
            $user = $this->getInfoByIDs( $user_id );

            if( isset($user[$user_id]) && !empty($user[$user_id]) )
            {
                if( time() < ( $user[$user_id]['last_activity'] + $this->session_lifetime ) )
                {
                    $this->_redis->set( 'user_'.$user_id.'_need_relogin', 1, 0, $this->redis_db_needrelogin );

                    return true;
                }
            }

            return false;
        }

        ///////////////////////////////////////////////////////////////////////
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
