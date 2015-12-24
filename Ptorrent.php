<?php
/**
 * Created by Marcosvto.
 * User: root
 * Date: 24/12/15
 * Time: 20:11
 */

require_once(dirname(__FILE__) . '/Bdecode.php');
require_once(dirname(__FILE__) . '/Bencode.php');

class Ptorrent{

    private $caminhoFile;
    private $decode;
    private $bencode;
    private $autor;
    private $nome_arquivo;
    private $announce;
    private $announce_list;
    private $hash;

    public function init($caminhoFile){
        $this->caminhoFile = $caminhoFile;
        $this->decode = new BDecode();
        $this->bencode = new Bencode();
        $this->decode->init($caminhoFile);
        $this->nome_arquivo = $this->decode->result['info']['name'];
        $this->announce = $this->decode->result['announce'];
        $this->announce_list = $this->decode->result['announce-list'];
        $this->hash = strtoupper(bin2hex(sha1($this->bencode->encode($this->decode->result['info']), true)));
    }

    public function getHash(){
         return $this->hash;
    }

    public function getNome_file(){
        return $this->nome_arquivo;
    }

    public function getAutor(){
        return $this->autor;
    }

    public function getMagnetLink(){
        // magnet: (Identifica o link magnético)
        // (Define que este é um link de torrents, para que você use programas como o BitTorrent)
        // (Hash do conteúdo)
       $magnet = 'magnet:?xt=urn:btih:';
       $hash = $this->hash;
       $nome = '&dn='.$this->nome_arquivo;           //&dn=name //(Nome do arquivo)
        //magnet:?xt=urn:btih:5dee65101db281ac9c46344cd6b175cdcad53426&dn=download
        return $magnet.''.$hash.''.$nome;

    }

    public function getSeedLounch(){

        if(!function_exists("curl_init")) die("cURL extension is not installed");
        $url = "http://bitsnoop.com/api/trackers.php?hash=" . $this->hash . "&json=1";
        $headers = array(
            'Host: bitsnoop.com',
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36',
            'Accept-Encoding: deflate,sdch',
            'Accept-Language: ru,en-US;q=0.8,en;q=0.6');

        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $r=curl_exec($ch);
        curl_close($ch);
        $lendo = json_decode($r,true);
        $aux= 0;
        $leechers =0;
        $seeds = 0;
        foreach($lendo as $key){
            foreach ($key as $value){
                if($aux == 1){
                    $seeds+= $value;
                    $aux++;
                }else if($aux == 2){
                    $leechers+=$value;
                    $aux++;
                }else{
                    $aux++;
                }
            }
            $aux= 0;
        }
        $data['seed'] = $seeds;
        $data['leecher'] = $leechers;
        return $data;
    }


}