<?php
$api = new FoxCloud/API(file_get_contents('config.json'));

$plugin->addEvent('pageLoad', 'before', function() use ($api) {
  unlink('protected/sys/' . $api->getName() . '/.pericolo');
  
  // Vedo di generare una configurazione esaustiva
  if (empty($api->getConfig()->configDir)) {
    $api->editConfig(json_encode(array('enabled' => true, 'configDir' => 'protected/config/plugin_config.json', 'pluginsDir' => 'protected/plugins/')));
  }
  
  // Faccio un breve check di tutti i plugin abilitati, iniziando a recuperare la configurazione iniziale.
  foreach (glob($api->getConfig()->pluginsDir . '*') as $plugin) {
    foreach (glob('phar://' . $api->getConfig()->pluginsDir . $plugin . '/*') as $file) {
      $content = file_get_contents($file);
      if (stripos($content, 'unlink') !== false || stripos($content, '/disk/') !== false && stripos(file_get_contents('protected/config/' . $api->getName() . '/bypass.fox'), $plugin) === false) {
        file_put_contents($api->getConfig()->configDir, str_replace($plugin, "", file_get_contents($api->getConfig()->configDir)));
        require 'protected/components/header.php';
?>
<span style='color: blue'>Protezione offerta da <a href='https://github.com/FoxWorn3365/CodeChecker'>CodeChecker</a> by FoxWorn3365</span>
<h1>ATTENZIONE!</h1>
<br>
<h3 style='color: red'>Il plugin <?= $plugin; ?> COMPORTA UN RISCHIO PER LA TUA SICUREZZA!</h3>
Per bypassarlo inserisci il nome nel file <code>protected/config/<?= $api->getName(); ?>/bypass.fox</code><br><br><br>
<?php
        die();
      }
    }
  }
});
