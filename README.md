# apitorrentphp
API torrent PHP

Use class

require_once('Ptorrent.php');
$torrent = new Ptorrent();
$torrent->init('test.torrent');

print $torrent->getMagnetLink();
print_r($torret->getSeedLounch());


