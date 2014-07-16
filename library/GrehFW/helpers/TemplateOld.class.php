<?php

class Template {

    private $vars = array();
    private $values = array();
    private $properties = array();
    private $instances = array();
    private $blocks = array();
    private $parents = array();
    private $accurate;
    private static $REG_NAME = "([[:alnum:]]|_)+";
    private static $instance = null;

    /**
     *
     * @return Template 
     */
    public static function getInstance($filename=null) {
        if (self::$instance == null && $filename != null) {
            self::$instance = new self($filename);
        }
        return self::$instance;
    }

    private function __construct($filename, $accurate = false) {
        $this->accurate = $accurate;
        $this->loadfile(".", $filename);
    }

    public function addFile($varname, $filename) {
        if (!$this->exists($varname))
            throw new InvalidArgumentException("addFile: var $varname não existe");
        $this->loadfile($varname, $filename);
        return $this;
    }

    public function __set($varname, $value) {
        if (!$this->exists($varname))
            throw new RuntimeException("var $varname não existe");
        $stringValue = $value;
        if (is_object($value)) {
            $this->instances[$varname] = $value;
            if (!array_key_exists($varname, $this->properties))
                $this->properties[$varname] = array();
            if (method_exists($value, "__toString"))
                $stringValue = $value->__toString();
            else
                $stringValue = "Object";
        }
        $this->setValue($varname, $stringValue);
        return $value;
    }

    public function __get($varname) {
        if (isset($this->values["{" . $varname . "}"]))
            return $this->values["{" . $varname . "}"];
        throw new RuntimeException("var $varname não existe");
    }

    public function exists($varname) {
        return in_array($varname, $this->vars);
    }

    private function loadfile($varname, $filename) {
        if (!file_exists($filename))
            throw new InvalidArgumentException("arquivo $filename não existe");
        $str = preg_replace("/<!---.*?--->/smi", "", file_get_contents($filename));
        $blocks = $this->recognize($str, $varname);
        if (empty($str))
            throw new InvalidArgumentException("arquivo $filename está vazio");
        $this->setValue($varname, $str);
        $this->createBlocks($blocks);
    }

    private function recognize(&$content, $varname) {
        $blocks = array();
        $queued_blocks = array();
        foreach (explode("\n", $content) as $line) {
            if (strpos($line, "{") !== false)
                $this->identifyVars($line);
            if (strpos($line, "<!--") !== false) {
                $this->identifyBlocks($line, $varname, $queued_blocks, $blocks);
            }
        }
        return $blocks;
    }

    private function identifyBlocks(&$line, $varname, &$queued_blocks, &$blocks) {
        $reg = "/<!--\s*BEGIN\s+(" . self::$REG_NAME . ")\s*-->/sm";
        preg_match($reg, $line, $m);
        if (1 == preg_match($reg, $line, $m)) {
            if (0 == sizeof($queued_blocks))
                $parent = $varname;
            else
                $parent = end($queued_blocks);
            if (!isset($blocks[$parent])) {
                $blocks[$parent] = array();
            }
            $blocks[$parent][] = $m[1];
            $queued_blocks[] = $m[1];
        }
        $reg = "/<!--\s*END\s+(" . self::$REG_NAME . ")\s*-->/sm";
        if (1 == preg_match($reg, $line))
            array_pop($queued_blocks);
    }

    private function identifyVars(&$line) {
        $r = preg_match_all("/{(" . self::$REG_NAME . ")((\-\>(" . self::$REG_NAME . "))*)?}/", $line, $m);
        if ($r) {
            for ($i = 0; $i < $r; $i++) {
                if ($m[3][$i] && (!array_key_exists($m[1][$i], $this->properties) || !in_array($m[3][$i], $this->properties[$m[1][$i]]))) {
                    $this->properties[$m[1][$i]][] = $m[3][$i];
                }
                if (!in_array($m[1][$i], $this->vars))
                    $this->vars[] = $m[1][$i];
            }
        }
    }

    private function createBlocks(&$blocks) {
        $this->parents = array_merge($this->parents, $blocks);
        foreach ($blocks as $parent => $block) {
            foreach ($block as $chield) {
//                if (in_array($chield, $this->blocks))
//                    throw new UnexpectedValueException("bloco duplicado: $chield");
                $this->blocks[] = $chield;
                $this->setBlock($parent, $chield);
            }
        }
    }

    private function setBlock($parent, $block) {
        $name = "B_" . $block;
        $str = $this->getVar($parent);
        if ($this->accurate) {
            $str = str_replace("\r\n", "\n", $str);
            $reg = "/\t*<!--\s*BEGIN\s+$block\s+-->\n*(\s*.*?\n?)\t*<!--\s+END\s+$block\s*-->\n?/sm";
        }
        else
            $reg = "/<!--\s*BEGIN\s+$block\s+-->\s*(\s*.*?\s*)<!--\s+END\s+$block\s*-->\s*/sm";
        if (1 !== preg_match($reg, $str, $m))
            throw new UnexpectedValueException("bloco $block está mal formado");
        $this->setValue($name, '');
        $this->setValue($block, $m[1]);
        $this->setValue($parent, preg_replace($reg, "{" . $name . "}", $str));
    }

    private function setValue($varname, $value) {
        $this->values["{" . $varname . "}"] = $value;
    }

    private function getVar($varname) {
        return $this->values['{' . $varname . '}'];
    }

    public function clear($varname) {
        $this->setValue($varname, "");
    }

    private function subst($varname) {
        $s = $this->getVar($varname);
        $s = str_replace(array_keys($this->values), $this->values, $s);
        foreach ($this->instances as $var => $instance) {
            foreach ($this->properties[$var] as $properties) {
                if (false !== strpos($s, "{" . $var . $properties . "}")) {
                    $pointer = $instance;
                    $property = explode("->", $properties);
                    for ($i = 1; $i < sizeof($property); $i++) {
                        $obj = str_replace('_', '', $property[$i]);
                        if (method_exists($pointer, "get$obj")) {
                            $pointer = $pointer->{"get$obj"}();
                        } elseif (method_exists($pointer, "is$obj")) {
                            $pointer = $pointer->{"is$obj"}();
                        } elseif (method_exists($pointer, "__get")) {
                            $pointer = $pointer->__get($property[$i]);
                        } else {
                            $className = $property[$i - 1] ? $property[$i - 1] : get_class($instance);
                            $class = is_null($pointer) ? "NULL" : get_class($pointer);
                            throw new BadMethodCallException("não existe método na classe " . $class . " para acessar " . $className . "->" . $property[$i]);
                        }
                    }
                    if (is_object($pointer)) {
                        if (method_exists($pointer, "__toString")) {
                            $pointer = $pointer->__toString();
                        } else {
                            $pointer = "Object";
                        }
                    }
                    $s = str_replace("{" . $var . $properties . "}", $pointer, $s);
                }
            }
        }
        return $s;
    }

    private function clearBlocks($block) {
        if (isset($this->parents[$block])) {
            $chields = $this->parents[$block];
            foreach ($chields as $chield) {
                $this->clear("B_" . $chield);
            }
        }
    }

    public function block($block, $append = true) {
        if (!in_array($block, $this->blocks))
            throw new InvalidArgumentException("bloco $block não existe");
        if ($append)
            $this->setValue("B_" . $block, $this->getVar("B_" . $block) . $this->subst($block));
        else
            $this->setValue("B_" . $block, $this->subst($block));
        $this->clearBlocks($block);
    }

    public function parse() {
        return preg_replace("/{(" . self::$REG_NAME . ")((\-\>(" . self::$REG_NAME . "))*)?}/", "", $this->subst("."));
    }

    public function show() {
        echo $this->parse();
    }

}