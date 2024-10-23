<?php

class data {
	
	// DB tables
	public static $db_tables = array(
			"SourceTV_Survival_Main",
			"SourceTV_Survival_LoggedEvents",
			"SourceTV_Survival_ConnectionLog",
			"SourceTV_Survival_PlayerClips",
			"OnLogAction_Logs",
			"gasconfigs_v2_logs"
	);
	
	/*
		This is used to update the sourceTV database
		with the correct names for each associated steamID.
	*/
	public static $player_aliases = array(
		"76561198067462280" => "dustin",
		"76561198083816716" => "angel (BRs)",
		"76561198081622889" => "gravity",
		"76561198000789017" => "scout",
		"76561198035973067" => "ULTRA",
		"76561198106076512" => "pocket",
		"76561198034747800" => "ben",
		"76561198056506628" => "deinemudda65",
		"76561198115279755" => "Sofield",
		"76561198144844926" => "Jalapeno",
		"76561197977159380" => "tosh",
		"76561198079189103" => "JAiZ",
		"76561198173242681" => "aintnocasual",
		"76561198201787828" => "Zoom",
		"76561198354326201" => "Fantashm",
		"76561198293634794" => "Iizvullok",
		"76561198842656621" => "aizenseske",
		"76561198042863251" => "Martyns",
		"76561198063404318" => "Shinzou-san",
		"76561198090950098" => "monamour",
		"76561198059735690" => "Lleage",
		"76561198866938654" => "Leo",
		"76561198088113157" => "Nexy",
		"76561198985655932" => "JSS",
		"76561197966073043" => "Azn",
		"76561198000681107" => "JeN",
		"76561198087095224" => "AmOrf PlaYa",
		"76561198965101242" => "jim",
		"76561198837527837" => "Fledermaus",
		"76561198338150854" => "YeRaZ",
		"76561198999831251" => "SimonPegg",
		"76561197977779376" => "bless",
		"76561197995769256" => "neoxx",
		"76561198372054161" => "Rouss",
		"76561198116002109" => "Im Serious",
		"76561198072355970" => "Sirrah",
		"76561198132214102" => "Eva (simmy, CeliAah)",
		"76561198172313918" => "Wesley258",
		"76561198147125400" => "dro",
		"76561197979082192" => "FALLingorflYING",
		"76561198132906264" => "AvovA",
		"76561198109136794" => "GreekLover",
		"76561198043298385" => "Blinky1_",
		"76561198005720534" => "Dissident",
		"76561198003961356" => "fufighter",
		"76561198268640429" => "Sazior",
		"76561198167149319" => "ish (Thundercat)",
		"76561198800356455" => "Little Sun",
		"76561198096808194" => "Goat Reliq",
		"76561198880351340" => "Doob",
		"76561198004329758" => "AnGeR (Paski)",
		"76561197960798528" => "RIN",
		"76561198086338214" => "Clover",
		"76561198044279942" => "zef",
		"76561198313246746" => "South London (aqz)",
		"76561198002070288" => "Licksore (Tankerbell)",
		"76561198157957747" => "Leesh",
		"76561198028741818" => "Gents",
		"76561198054729247" => "Joshman KB",
		"76561198075676109" => "LuckySpock",
		"76561198079469097" => "bella (CJ)",
		"76561197984938312" => "Overdose",
		"76561197981071135" => "bleeds",
		"76561198267446103" => "Archer",
		"76561198210264817" => "Hysterisch",
		"76561198016049158" => "AKA Ruthless Killer",
		"76561198044935058" => "MistLight",
		"76561198038758341" => "R0m4n",
		"76561198035414786" => "Zummino",
		"76561198145048547" => "Bets",
		"76561198043149695" => "KTH23z",
		"76561198018795247" => "dr Spezza",
		"76561198045838981" => "Davis",
		"76561198006588157" => "kris",
		"76561198085462698" => "Light",
		"76561198034562983" => "N",
		"76561198033041889" => "Wilson2234",
		"76561198116585225" => "henry (Raptor)",
		"76561198231965792" => "Guz",
		"76561198119624204" => "SunDres",
		"76561198120110145" => "gangster0x",
		"76561198163133488" => "Pickle",
		"76561198129114619" => "phil (Pistola)",
		"76561198123202956" => "Justin (Australian Spy)",
		"76561198008381159" => "The Happy Falafel",
		"76561198003184616" => "J.T.",
		"76561198078887961" => "SK",
		"76561198037765926" => "Rambo",
		"76561198018599806" => "Eph Air",
		"76561198014194617" => "MudFlap",
		"76561198019156301" => "OmegaMan",
		"76561198066296388" => "Orion",
		"76561198844275558" => "Maplestrike",
		"76561198446698838" => "John Bender",
		"76561198337867927" => "RageRebel",
		"76561197982367807" => "Niko Bellic",
		"76561198371665066" => "Elzbieta Bosak",
		"76561198010153092" => "skiddy",
		"76561198094867782" => "Term010",
		"76561198048910940" => "Taxi Service",
		"76561198157255548" => "Tail",
		"76561198202838681" => "Grantt",
		"76561197970134922" => "[5150]",
		"76561198052603976" => "E2 (BuQeT, Buqelicious)",
		"76561198004962322" => "trash",
		"76561198155783469" => "tee",
		"76561197972953244" => "Distress",
		"76561198072417027" => "ThePeaceDove",
		"76561198056582175" => "dolphin",
		"76561198322387009" => "Die Demon",
		"76561198298486800" => "dead",
		"76561198079970003" => "zerk",
		"76561198042413584" => "Russell Freeman",
		"76561198071666755" => "lucifeR",
		"76561198014940495" => "Cowwy (Mr.Cow, Mooey)",
		"76561198060123351" => "HellishWoman",
		"76561197970676789" => "Cheekadaweek",
		"76561198877521182" => "LIEQUDE",
		"76561197994287270" => "Nesisoth",
		"76561197983568792" => "Anker",
		"76561198204319763" => "Cat Woman",
		"76561198054271193" => "PinkPotato (Skooshy)",
		"76561198019101111" => "Toad",
		"76561198119523998" => "Azealia",
		"76561198030133681" => "Hata",
		"76561198038210076" => "Look ma no Hands",
		"76561198080528081" => "Kael",
		"76561197979489003" => "KrAyZ",
		"76561198310171379" => "Suprise",
		"76561198014576128" => "Mr. Man!",
		"76561198814099665" => "S A I N (SAIN)",
		"76561198098171907" => "The Blitz",
		"76561198030074508" => "msoldier13",
		"76561198202772881" => "Flow (Pancakes)",
		"76561198067307730" => "Alexthesniper19",
		"76561198063975302" => "Chillplayer",
		"76561197984651631" => "CrueL",
		"76561197972263334" => "LootChaser",
		"76561198014687600" => "Mr.Ed",
		"76561197975717871" => "The Poopsons",
		"76561198317720202" => "swing",
		"76561198872186448" => "k1ller",
		"76561199091748811" => "Sweet Potato Pie",
		"76561198241421466" => "Lisa",
		"76561197998967034" => "Grymnir",
		"76561198387068532" => "veyr",
		"76561197960272161" => "Schemer",
		"76561198017657229" => "Tom Ren",
		"76561198046975912" => "Nameless", 
		"76561198124900228" => "world",
		"76561198018743132" => "ladyankh",
		"76561198045837761" => "nekov4ego",
		"76561198139380835" => "bebe",
		"76561198120223045" => "Hyebe",
		"76561198293307156" => "GGMaster",
		"76561198052829302" => "G",
		"76561198072390256" => "reimu",
		"76561197985604741" => "moomoocow (Tom)", 
		"76561197994805459" => "Sniper Dude",
		"76561197990196235" => "Horde",
		"76561198068809892" => "Hugga",
		"76561199012524333" => "weeb",
		"76561198332859660" => "StarLord1999",
		"76561199091211123" => "DAGGER",
		"76561198057477717" => "erokiti",
		"76561198309164563" => "Hyper",
		"76561197992993816" => "dough",
		"76561198125954052" => "FunkyDude", 
		"76561198138318026" => "nade",
		"76561198067988904" => "Gamma",
		"76561198037994859" => "Hitman",
		"76561198000468628" => "atticus",
		"76561197970841975" => "hellbert",
		"76561198259765589" => "Simplicity",
		"76561198282766118" => "Werewolf",
		"76561197962664348" => "usantahl",
		"76561198034375627" => "ScAVENGE",
		"76561197990175370" => "Bugagadze",
		"76561198017507425" => "Jill",
		"76561197970633684" => "Herc7",
		"76561198245957624" => "DMM",
		"76561198804216560" => "Polarized",
		"76561198173851239" => "foggrrr",
		"76561198119893314" => "Shy",
		"76561198093714585" => "Boss #1 (Faceless)",
		"76561198083040552" => "Resist",
		"76561198283641449" => "Momo",
		"76561198096947785" => "GREATFULL",
		"76561198064444658" => "Skyheart",
		"76561198124258845" => "m.j",
		"76561198012181770" => "Sun 1349",
		"76561198009944351" => "skeleton",
		"76561198370600308" => "IGR J17091-3624",
		"76561198874224838" => "No Jumping",
		"76561198308053030" => "figurante",
		"76561198064520296" => "Kate",
		"76561198003024687" => "krazyeights",
		"76561198027102231" => "Kaibil",
		"76561199067772267" => "164",
		"76561198021822493" => "r68mmj",
		"76561198976237994" => "Gris",
		"76561198003744460" => "itsNyash",
		"76561198011786361" => "SCaliN",
		"76561198269436948" => "rd",
		"76561198996120403" => "toad",
		"76561198319673484" => "Gumbo",
		"76561198017082063" => "Abdur",
		"76561198427792186" => "Kat",
		"76561198801106583" => "Phantom (187)",
		"76561198015094232" => "Azimuth",
		"76561197989984935" => "Mr.Alexfrlns",
		"76561198116662352" => "Auntie Sam",
		"76561198037162401" => "gaussian_noise",
		"76561198374167777" => "Hornet",
		"76561198411010566" => "Ralimu Strilem",
		"76561197996186819" => "phoenix_advance",
		"76561198017158853" => "Hom67",
		"76561198044181880" => "BaMM",
		"76561198101836257" => "khan (drem)",
		"76561198069859323" => "Kaigin",
		"76561198937807426" => "xz",
		"76561198050736005" => "Shanks",
		"76561198097700212" => "vk1",
		"76561198804077912" => "Flamming",
		"76561197968802052" => "FLUX",
		"76561198012461794" => "K I C K E R",
		"76561198004590585" => "Lynasart",
		"76561198010807488" => "s43ko",
		"76561198035757304" => "HeXaah",
		"76561199048362870" => "Sh4dow",
		"76561198021324770" => "suniki",
		"76561197962342292" => "Coincident",
		"76561198404559997" => "Coa",
		"76561198139535622" => "Vestrocity",
		"76561198831874360" => "Mr.Py (python)",
		"76561198079863290" => "KrankZ",
		"76561198371378953" => "Ashanna",
		"76561198309087430" => "Fallen Angel (Crash Override)",
		"76561199094317013" => "Hazzard",
		"76561198353895793" => "Fenix",
		"76561197990099252" => "Danilicius",
		"76561197981727999" => "sLeeK",
		"76561197999811069" => "bunni",
		"76561198803592090" => "Noob Saibot",
		"76561198006221004" => "Shadow_Rico",
		"76561198178025655" => "Malthe",
		"76561198281830301" => "Chiper_007",
		"76561198064060597" => "adasdasd",
		"76561198936754072" => "Spuddley",
		"76561198156331839" => "Tako",
		"76561198060522148" => "Jajami_O_Mate",
		"76561198069430628" => "Nathan",
		"76561199193113427" => "hkl8",
		"76561198041122092" => "-Eq-reNyYyYyYyYyYyY",
		"76561198018344984" => "SLAP PACKAGE",
		"76561198181939463" => "+zero",
		"76561198263928255" => "Vogeltek",
		"76561198128163562" => "Heeb",
		"76561199065670640" => "dcutbtw",
		"76561198151331857" => "мár'c",
		"76561198076564951" => "MUST be NICE",
		"76561199062351778" => "Sallen",
		"76561198968900763" => "ZokkuZ",
		"76561198839661474" => "Myelinated23",
		"76561198060026108" => "Soccerlegend23",
		"76561198057903283" => "Edga",
		"76561198080776508" => "coZ",
		"76561198064817566" => "Hawkens",
		"76561199141205551" => "Blakanuse",
		"76561198032105883" => "Dibbo",
		"76561198064250076" => "Exerude",
		"76561198981608253" => "Josh",
		"76561199067172666" => "Envyy",
		"76561199337079379" => "ei ~♫",
		"76561197985496423" => "DEG",
		"76561198054275169" => "Skeletor",
		"76561198377648497" => "Bullet",
		"76561198383686405" => "Phantom (Robin)",
		"76561199137963364" => "Luluzephyr",
		"76561199375685838" => "Mia",
		"76561198145577813" => "PaiGOD",
		"76561198139383560" => "Hype",
		"76561198041730828" => "Sesh",
		"76561198113752650" => "BadPlayer",
		"76561198953406488" => "Micky",
		"76561198029763066" => "Rolano",
		"76561198825697705" => "RaGe",
		"76561199092743978" => "Luna",
		"76561199162937196" => "Alucard",
		"76561199139784485" => "Ͳ·Ͳ (T.T)",
		"76561198025234110" => "Wuwu",
		"76561198139512645" => "Undergo",
		"76561198218186077" => "J9",
		"76561198023897982" => "fudge_it",
		"76561198079289142" => "dom",
		"76561198140306677" => "juicer",
		"76561198897691784" => "-TRiX-",
		"76561198393103579" => "grandass",
		"76561199031464772" => "Lopey",
		"76561198072627979" => "Sin (Sintex)",
		"76561198002684069" => "3BrainCells",
		"76561198420486066" => "or",
		"76561199126223161" => "AKA Ruthless Killer",
		"76561198079582732" => "Master_64",
		"76561199013379456" => "dumbass",
		"76561198451442151" => "LukanBBY",
		"76561198127397924" => "Krivi",
		"76561199276719851" => "Forger",
		"76561198798707751" => "Dante",
		"76561199103866424" => "Itelier",
		"76561199036235583" => "Sonata",
		"76561198018706932" => "Old Hermit",
		"76561198022498965" => "JaS1LdQ[T]",
		"76561199338981955" => "doomguy",
		"76561198210934887" => "love maze",
		"76561197978506223" => "Nayuta",
		"76561198272114920" => "Zondar",
		"76561198136877812" => "jaku",
		"76561198436737304" => "elle",
		"76561198026039452" => "Keegan",
		"76561198862218560" => "DRN0167",
		"76561198061306688" => "Ninjahumanbeing23",
		"76561199130413814" => "Starsan64",
		"76561198130203570" => "Dziublan",
		"76561198263510579" => "-Yuan_.",
		"76561199052474367" => "Hysteria",
		"76561198010464399" => "hantsuki",
		"76561198894489305" => "Korn",
		"76561199270247984" => "manga",
		"76561198169805453" => "RGD",
		"76561198313415399" => "n1ko",
		"76561198131653885" => "ServerTourist",
		"76561198369320985" => "Ada",
		"76561198350507483" => "lianju",
		"76561199384830911" => "tacticalblitz",
		"76561198309700520" => "J.R.",
		"76561199013668699" => "kasu",
		"76561199219800538" => "ClownBalls2",
		"76561199061117052" => "may",
		"76561198425552176" => "Pokazu",
		"76561198051299733" => "Slimy King",
		"76561198304848542" => "海源Ockun",
		"76561199061196081" => "lyn",
		"76561199130856870" => "jane",
		"76561199444879911" => "bakednoodle",
		"76561198349461317" => "Chocolate Carrots",
		"76561199679601275" => "miumi",
		"76561198432334501" => "Elesis",
		"76561198875950860" => "Gauntlet",
		"76561198130472393" => "mei",
		"76561198216921080" => "Котяра",
		"76561199049022782" => "Mr Anime",
		"76561198251089320" => "Jes"
	);
	
	/*
		Deleted steam accounts - skip over these
		If $player_aliases contains any of these keys, KeyCheck.sh will pick it up as duplicate entry
	*/
	public static $steamid_ignore = array(
		"76561199185319520",
		"76561199225468782",
		"76561199214684666",
		"76561199225435822",
		"76561199213551468",
		"76561198841030367",
		"76561198958878897", // sk9el
		"76561199234809406",
		"76561199238203344",
		"76561199242469687",
		"76561197998177692", // ★Thug-Life★
		"76561198379763019"
	);
}
