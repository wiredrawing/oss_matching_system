<?php

namespace App\Library {



    class Logger
    {

        /**
         * info情報としてログ情報を保存する
         *
         * @param string $logging_message
         * @param array $dump_data
         * @return void
         */
        static public function info (string $logging_message = "", $dump_data = [])
        {
            logger()->info(PHP_EOL);
            // logger()->info(str_pad("", 100, ">"));
            logger()->info($logging_message);
            logger()->info($dump_data);
            // logger()->info(str_pad("", 100, "<"));
            logger()->info(PHP_EOL);
        }

        /**
         * error情報としてログ情報を保存する
         *
         * @param string $logging_message
         * @param array $dump_data
         * @return void
         */
        static public function error (string $logging_message = "", $dump_data = [])
        {
            logger()->info(PHP_EOL);
            // logger()->error(str_pad("", 100, ">"));
            logger()->error($logging_message);
            logger()->error($dump_data);
            // logger()->error(str_pad("", 100, "<"));
            logger()->info(PHP_EOL);
        }
    }
}
