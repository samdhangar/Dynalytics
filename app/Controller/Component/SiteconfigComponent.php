<?php

class SiteconfigComponent extends Component
{
    var $controller = null;

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $this->settings = $settings;
        parent::__construct($collection, $settings);
        $this->SetconfigComponent();
    }

    function SetconfigComponent()
    {
        if (Configure::read("Site.Name")) {
            return true;
        }
        $data = Cache::read('siteConfig');
        if (empty($data)) {
            $data = ClassRegistry::init("SiteConfig")->find('list');
            Cache::write('siteConfig', $data);
        }
        $usedSnippet = array();
        foreach ($data as $key => $value) {
            preg_match_all('/{{(.*?)}}/is', $value, $matches);
            if (!empty($matches[1])) {
                $usedSnippet[$key] = $value;
            } else {
                Configure::write($key, $value);
            }
        }
        foreach ($usedSnippet as $key => $value) {
            preg_match_all('/{{(.*?)}}/is', $value, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $k => $v) {
                    if (strtolower(substr($_SERVER['REQUEST_URI'], 0, 6)) != '/admin') {
                        $value = str_replace("{{" . $v . "}}", Configure::read($v), $value);
                    }
                }
            }
        }
    }
}

?>
