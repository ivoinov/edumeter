<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Seven
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    class Seven_File_Reader_Mo extends Seven_Object {

        protected $_file;
        protected $_endian = 'little';
        protected $_mb_enable = false;

        public function __construct() {
            $this->_mb_enable = ((ini_get("mbstring.func_overload") & 2) != 0) && function_exists('mb_substr');
        }

        public function open($file) {
            $this->_file = fopen($file, "r");
            if(!$this->_file)
                throw new Exception("Can not open MO file");
            return $this;
        }

        protected function _fseek($offset = 0) {
            if(fseek($this->_file, $offset, SEEK_SET) === -1)
                throw new Exception("File offset not exists");
            return $this;
        }

        protected function _read($length) {
            if(!$length) return "";
            return fread($this->_file, $length);
        }

        protected function _readInt32() {
            $bytes = $this->_read(4);
            if($this->strlen($bytes) != 4)
                return false;
            $endian_letter = ($this->_endian == 'big') ? 'N' : 'V';
            $int = unpack("L", $bytes);
            return array_shift($int);
        }

        public function load(&$data) {
            $this->_fseek(0);
            // Read Magic number
            $magic = $this->_readInt32();
            if($magic != 0x950412de && $magic != -1794895138)
                throw new Exception("Magin number is incorrect");
            // Read revision
            $this->setRevision($this->_readInt32());
            // Read string number
            $this->setStringNumber($this->_readInt32());
            // Read offset of original strings table
            $this->setOriginalOffset($this->_readInt32());
            // Read offset of translation strings table
            $this->setTranslationOffset($this->_readInt32());
            // Read size of hashing table
            $this->setSizeHashing($this->_readInt32());
            // Read offset of hashing table
            $this->setOffsetHashing($this->_readInt32());
            // Read strings
            $data = $this->_readStrings();

            return $this;
        }

        protected function _readStrings() {
            $data = array();
            // get offsets
            $N = $this->getStringNumber();
            $O = $this->getOriginalOffset();
            $T = $this->getTranslationOffset();
            $S = $this->getSizeHashing();
            $H = $this->getOffsetHashing();
            $headoffset = 0;
            // read strings
            for($i = 0; $i < $N; $i++) {
                // get strid
                $this->_fseek($O + $i * 8);                
                $length = $this->_readInt32();
                $this->_fseek($this->_readInt32());
                $string = $this->_read($length);
                // get strmsg
                $this->_fseek($T + $i * 8);
                $length = $this->_readInt32();
                $this->_fseek($this->_readInt32());
                $translation = $this->_read($length);
                // set data
                if($string == "") {
                    $this->setHeaders($translation);
                } else {
                    foreach(explode("\0", $string) as $string_form) {
                        $forms = explode("\0", $translation);
                        $data[$string_form] = (count($forms) < 2) ? reset($forms) : $forms;
                    }
                }
            }

            return $data;
        }

        public function setHeaders($headers) {
            if(!is_array($headers)) {
                $headers_array = array();
                foreach(explode("\n", $headers) as $header_raw) {
                    if(strpos($header_raw, ':') === false) continue;
                    list($key, $value) = explode(':', $header_raw,2);
                    $headers_array[strtolower(str_replace('-', '_', trim($key)))] = trim($value);
                }
                $headers = $headers_array;
            }
            return parent::setHeaders($headers);
        }
        
        public function getHeader($key) {
            $headers = $this->getHeaders();
            if(isset($headers[$key]))
                return $headers[$key];
            return null;
        }
        
        public function close() {
            fclose($this->_file);
            return $this;
        }

        protected function strlen($string) {
            if($this->_mb_enable)
                return mb_strlen($string);
            return strlen($string);
        }

    }