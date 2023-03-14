<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\ScannedBasket;
use App\Services\Parser\SparParserService;

class SparParserServiceTest extends TestCase
{

    const SAMPLE_01 = 'Esamin Hungary Kit.
1133 BUDAPEST
POZSONYI UT 46 FSZ en. 2 aito

1085 budapest
Jozsef korut 30
ADOSZAM: 26289201-2:-41

A

AFA Megnevezés

Nemy. Me. Esyscoat | Erték
00 PW, CSIPKEBOOYO TEA SOG 789
COO PW VARTACIO <ZoLD) 306 78°
COO PICK SERTES PAR. 100G+11 629
COO S-BUDGET P.SOS MOGY.S00G 999
BOO SPAR VIDEKI CIFO SOO G 599
A PICK SERTES PAR 1006+ 11 629

BANKKARTYA: 4 434 Ft

20KKKKOOKK HUSEGPONT INFORMACTO  xxxx 00K
SSZES HUSEGPONT : 4
HOO UII OR III CK RIOR RIK RK IR OK OR
KOSZONJUK A YASARLAST! VY, SPAR. HU
A NETA KOTELEZETISEG AZ ELADOT TERHELI
KOZV.SZOLG. ESETEN AZ AFA TV. 160. 8 SZERINT
TRI IKK KIRK IK RIOR IK IK DORR RIK IRR BR HOR IK

TRSZ: 814860 PENZTAR: 103 PENZTAROS: 901511

NYUGTASZAM: 1641/010393
2023.02.24. 11:48

NAV Ellenorz6 kod: GFA7F
#PA3102704';

    const EXPECTED_01 = [
        'basketId' => '1641/010393',
        'taxNumber' => '26289201-2:-41',
        'marketAddress' => '1085 budapest Jozsef korut 30',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYI UT 46 FSZ en. 2 aito',
        'companyName' => 'Esamin Hungary Kit.',
        'date' => '2023.02.24. 11:48',
        'total' => ' 4 434',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'W, CSIPKEBOOYO TEA SOG',
                'price' => '789',
                'unit_price' => 789.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'PW VARTACIO <ZoLD) 306',
                'price' => '78',
                'unit_price' => 78.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'PICK SERTES PAR. 100G+11',
                'price' => '629',
                'unit_price' => 629.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'S-BUDGET P.SOS MOGY.S00G',
                'price' => '999',
                'unit_price' => 999.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'SPAR VIDEKI CIFO SOO G',
                'price' => '599',
                'unit_price' => 599.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'CK SERTES PAR 1006+',
                'price' => '11629',
                'unit_price' => 11629.0,
            ],
        ],
    ];

    const SAMPLE_02 = 'Esznin Hungary Kt,
1133 BUDAPEST
POZSONYT UT 48 FSZ em. 2 aito

1085 Budapest
Jozsef korut 50
ADOSZAM: 26289201--2--41

er Qa —

COO PW.CSIPKEBOGYO TEA 50G 789

COO PW VARTACIO (Z0LD) 306 789
COO 3BIT SZELET 466; 204
COO 3BIT SZELET 466 204
COO ROYAL VIRSLT SERTES 400 2 089
BOO SPAR EPRES MUZLI 2006 599
BOO SPAR GOROG JOGHURT 400(; 449
COO S-BUDGET P.S6S MosY .500IG 999
ADO JUMBO FRISS TOJAS 10 Dif} 1 139
BOO SPAR VIDEKI CIPO 500 ¢ 659
ie enn
ISSZESAN: € (20 Ft
BANKKARTYA € 020 Ft
HRRKKK KIRK HUSEGPONT INFORMACIO xxxxxx Ix
OSSZES HUSEGPONT : 8

TOXIC OOO ORRICK ORR KI III I
KOSZONJUK A YASARLAST! WHY, SPAR. HU
A NETA KOTELEZETISEG AZ ELADOT TERHELT
KOZV.SZOLG. ESETEN AZ AFA TV. 160, § SZERINT
OOOO OOOO SOTO II IIR

TRSZ: 805164 PENZTAR: 103 PENZTAROS: 901222

NYUGTASZAM: 1631/00399
2023.02.14. 10:45

NAY Ellenérz6 kod:2548e
APAI3102704';

    const EXPECTED_02 = [
        'basketId' => '1631/00399',
        'taxNumber' => '26289201--2--41',
        'marketAddress' => '1085 Budapest Jozsef korut 50',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYT UT 48 FSZ em. 2 aito',
        'companyName' => 'Esznin Hungary Kt,',
        'date' => '2023.02.14. 10:45',
        'total' => ' € 020',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '789',
                'name' => 'PW.CSIPKEBOGYO TEA 50G',
                'unit_price' => 789.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '789',
                'name' => 'PW VARTACIO (Z0LD) 306',
                'unit_price' => 789.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466;',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '2089',
                'name' => 'ROYAL VIRSLT SERTES 400',
                'unit_price' => 2089.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '599',
                'name' => 'SPAR EPRES MUZLI 2006',
                'unit_price' => 599.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '449',
                'name' => 'SPAR GOROG JOGHURT 400(;',
                'unit_price' => 449.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '999',
                'name' => 'S-BUDGET P.S6S MosY .500IG',
                'unit_price' => 999.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1139',
                'name' => 'JUMBO FRISS TOJAS 10 Dif}',
                'unit_price' => 1139.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '659',
                'name' => 'SPAR VIDEKI CIPO 500 ¢',
                'unit_price' => 659.0,
            ],
        ],
    ];

    const SAMPLE_03 = 'Esonin Hungary Kit.
1133 BUDAPEST
POZSONYT UT 46 FSZ em. 2 ajto

1085 Budapest
Jozsef korut 50
ADOSZAM: 26289201-2-41

pis; =

COO ROYAL VIRSLI SERTES 40006 2 O89

BOO S-BUDGET TEJFOL. 20%4900 739
COO 3BIT SZELET 466 204
COO 3BIT SZELET 466 204
BOO SPAR DARABOLT TRAPPISTA
(1,236 KG x 4 490 Ft/KG 1 060
BOO SPAR VIDEKI CIPO 500 & 629
C00 S-BUDGET P.S0S MOGY .SOLG 999
BOO DUBLIN DA. TR VAS 2006 41 29
RESZOSSZESEN 7 349 Ft
COO CLIPPER TUZKOVES ONGYULT 464
(SSZESnk 7 ait
BANKKARTYA 7 813 Ft

HARK KKKKAK HUSEGPONT TNFORMACIO KAKKKKARK
(ISSZES HUSEGPONT « 7
RRR KRRRAKERKIKKERE ARIE KEK RRARE KABA
KOSZONJUK A VASARLAST! ily. SPAR. HU
A NETA KOTELEZETISEG AZ ELADOT TERHELT
KOZV.SZ0LG. ESETEN Az AFA TV. 160. § SZERINT

MRK KKK KKKKKKAKRKA KAA RA KRERKKAD HICK RAK KKK RAR

TRSZ; 808265 PENZTAR: 103 PENZTAROS: 9C1511

NYUGTASZAM: 1634/(10347
2023.02.17. 10:31
NAV Ellenorz6 kod:tF 703
APag3102704';

    const EXPECTED_03 = [
        'basketId' => '1634/(10347',
        'taxNumber' => '26289201-2-41',
        'marketAddress' => '1085 Budapest Jozsef korut 50',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYT UT 46 FSZ em. 2 ajto',
        'companyName' => 'Esonin Hungary Kit.',
        'date' => '2023.02.17. 10:31',
        'total' => ' 7 813',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '289',
                'name' => 'ROYAL VIRSLI SERTES 40006',
                'unit_price' => 289.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '739',
                'name' => 'S-BUDGET TEJFOL. 20%4900',
                'unit_price' => 739.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1.236,
                'quantity_unit_id' => 1,
                'price' => '060',
                'name' => 'SPAR DARABOLT TRAPPISTA',
                'unit_price' => 48.54368932038835,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '629',
                'name' => 'SPAR VIDEKI CIPO 500 &',
                'unit_price' => 629.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '999',
                'name' => 'S-BUDGET P.S0S MOGY .SOLG',
                'unit_price' => 999.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '4129',
                'name' => 'DUBLIN DA. TR VAS 2006',
                'unit_price' => 4129.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '464',
                'name' => 'CLIPPER TUZKOVES ONGYULT',
                'unit_price' => 464.0,
            ],
        ],
    ];

    const SAMPLE_04 = '{sanin Hungary Kft,

1133 BUDAPEST
POZSONYI UT 48 FS7 em, 2 ajto

SPAR HARKET

1085 Budapest
Jozsef korut 50
ADOSZAM: 26289201-2-41

COO HAPPY FR. CHILTS BAB 240G 619
COO HAPPY FR.CHILTS Bap 2406 619
COO HF FEHERBAB PSz0sz 400G 649
COQ SPAR SUR.PAR. UV’. 2006 364
COO RAUCHJUICEB ANANARMARSID 1999
COO SBIT SZELET 46¢ 204
COO SBIT SZELET -46¢ 204
COO ROYAL WIRSLI SERTES 4006 1 699
BOO SPAR VIDEKI CIPo 900 G 969
COO GYULAT KOLBASZ 2506 VG 1 599
COO LEVESZLDSEG 1kG 899
CUO ALMA GOLDEN KG

0,621 KG x 329 Ft/KG 204
COQ BURGONYA ETK, PIROS KG

0.476 KG x 399 Ft/KG 190

(sre: Cf

BANKKART YA S 318 Ft

PARRAKRKK HUSEGPONT INFORMAGTO AARAKKAKK

OSSZES HUSEGPONT : 9

NMI AXXX RI RR RAI HIRRERA IW REX RKARKKOCI
KOSZONJUK A VASARLAST! WW. SPAR. HU
A NETA KOTELEZETTSEG AZ ELADOT TERHELT
KOZV.SZ0LG. ESETEN AZ AFA TV, 160. § SZERINT

FR OEKIOE IIT IOI IIR OOUR Ida

TRSZ: 668830) PENZTAR: 103 PENZTAROS: 901512

NYUGTASZAM: 1462/00657
2022.09.16. 18:15

NAV El lenorz6 kéd: 10231
4PAN3102704';

    const EXPECTED_04 = [
        'basketId' => '1462/00657',
        'taxNumber' => '26289201-2-41',
        'marketAddress' => '1085 Budapest Jozsef korut 50',
        'marketName' => 'SPAR HARKET',
        'companyAddress' => '1133 BUDAPEST POZSONYI UT 48 FS7 em, 2 ajto',
        'companyName' => '{sanin Hungary Kft,',
        'date' => '2022.09.16. 18:15',
        'total' => ' YA S 318',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '619',
                'name' => 'HAPPY FR. CHILTS BAB 240G',
                'unit_price' => 619.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '619',
                'name' => 'HAPPY FR.CHILTS Bap 2406',
                'unit_price' => 619.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '649',
                'name' => 'HF FEHERBAB PSz0sz 400G',
                'unit_price' => 649.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '364',
                'name' => 'SPAR SUR.PAR. UV’. 2006',
                'unit_price' => 364.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1999',
                'name' => 'RAUCHJUICEB ANANARMARSID',
                'unit_price' => 1999.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => 'SBIT SZELET 46¢',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => 'SBIT SZELET -46¢',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1699',
                'name' => 'ROYAL WIRSLI SERTES 4006',
                'unit_price' => 1699.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '969',
                'name' => 'SPAR VIDEKI CIPo 900 G',
                'unit_price' => 969.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1599',
                'name' => 'GYULAT KOLBASZ 2506 VG',
                'unit_price' => 1599.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '899',
                'name' => 'LEVESZLDSEG 1kG',
                'unit_price' => 899.0,
            ],
            [
                'quantity' => 0.621,
                'quantity_unit_id' => 1,
                'price' => '204',
                'name' => 'ALMA GOLDEN KG',
                'unit_price' => 328.5024154589372,
            ],
            [
                'quantity' => 0.476,
                'quantity_unit_id' => 1,
                'price' => '190',
                'name' => 'BURGONYA ETK, PIROS KG',
                'unit_price' => 399.15966386554624,
            ],
        ],
    ];

    const SAMPLE_05 = 'Esznin Hungary ft,

1133 BUDAPEST
POZSONYI UT 4g FSZ em. 2 aito

1085 Budapest
Jozsef kéruit 50
ADOSZAM: 26289201-2-41

BOO RISTORANTE DIAVOLA 3506 1 340
COO LAYS HAGYMA-TEJF 1406 649
BOO SPAR VIDEKT CIPO 500 G 069
CUO LIPTON EARL GREY 50x1.5G 1 340
BOO S-BUDGET TEJFo) 2024506 659
(GSES: 4a) Ft
BANKKARTYA 4 553 Ft

XXX KKK HISEGPONT INFORMACTO AXRKKKAXK
OSSZES HUSEGPONT :
ee ORR a cy
KOSZ0NJUK A VASARLAST! HWW. SPAR. HU
A NETA KOTELEZETTSEG AZ ELADOT TERHELT
KOZV.S70LG, ESETEN AZ AFA TV. 160. § SZERINT
TRG ARREXRRRAARRANKRRRMUCRARRERRLEREES

TRSZ: 681385 PENZTAR: 103 PENZTAROS: 901512

NYUGTASZAM: 1496/00699
2022.09.30, 18:02

NAV El lenérz6 kod: 880CA
4PAN3102704
';

    const EXPECTED_05 = [
        'basketId' => '1496/00699',
        'taxNumber' => '26289201-2-41',
        'marketAddress' => '1085 Budapest Jozsef kéruit 50',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYI UT 4g FSZ em. 2 aito',
        'companyName' => 'Esznin Hungary ft,',
        'date' => '2022.09.30, 18:02',
        'total' => ' 4 553',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1340',
                'name' => 'RISTORANTE DIAVOLA 3506',
                'unit_price' => 1340.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '649',
                'name' => 'LAYS HAGYMA-TEJF 1406',
                'unit_price' => 649.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '069',
                'name' => 'SPAR VIDEKT CIPO 500 G',
                'unit_price' => 69.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1340',
                'name' => 'LIPTON EARL GREY 50x1.5G',
                'unit_price' => 1340.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '659',
                'name' => 'S-BUDGET TEJFo) 2024506',
                'unit_price' => 659.0,
            ],
        ],
    ];

    const SAMPLE_06 = 'Esmmin Hungary Kt.
1133 BUDAPEST
POZSONYI UT 4g FSZ em. 2 aito

1085 Budapest
Jozsef korut 50
ADOSZAM: 716289201-2-41

COO PICK SERTES PAR. 100G+11 969
C00 PICK SERTES PAR. {O0G+11 969
COO PICK SERTES PAR. {006+11 969
BOO RISTORANTE DIAVOLA S006 1 340
COO SPAR EPER.2006 729
AQO MAGYAR ESL. DOB. TEJ2,8%1L 629
BOO SZENDVICS VEKNI 3506 9/9
COO 3BIT SZELET 466 204
COO 3BIT SZELET 466 204
BOO SPAR CSASZARZSEMLE (KF) 99
BOO SPAR CSASZARZSEMLE (KF) K
(SSS: * Bll Ft
BANKKARTYA 5 610 Ft

HxKKKKARRK HUSEGPONT TNFORMACIO  xxxxxxxx%
QSSZES HUSEGPONT : 9
GRRKKKUREKIRKIREREI IERIE KIRA IA RBI
KOSZ0NJUK A VASARLAST ! yi SPAR HU
A NETA KOTELEZETTSEG AZ ELADOT TERHELT
KOzv.SZ0LG. ESETEN AZ AFA TV. 160. § SZERINT
CRI HIKARI ERIIREEDAII ADEE III IIIA

TRSZ: 686665 PENZTAR: 103 PENZTAROS: 9014¢1

NYUGTASZAM: 1502/00561
2022.10.06. 17:43

NAV El lenérz6 kod: 4819E
APANB102704';

    const EXPECTED_06 = [
        'basketId' => '1502/00561',
        'taxNumber' => '716289201-2-41',
        'marketAddress' => '1085 Budapest Jozsef korut 50',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYI UT 4g FSZ em. 2 aito',
        'companyName' => 'Esmmin Hungary Kt.',
        'date' => '2022.10.06. 17:43',
        'total' => ' 5 610',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '969',
                'name' => 'PICK SERTES PAR. 100G+11',
                'unit_price' => 969.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '969',
                'name' => 'PICK SERTES PAR. {O0G+11',
                'unit_price' => 969.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '969',
                'name' => 'PICK SERTES PAR. {006+11',
                'unit_price' => 969.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1340',
                'name' => 'RISTORANTE DIAVOLA S006',
                'unit_price' => 1340.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '729',
                'name' => 'SPAR EPER.2006',
                'unit_price' => 729.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '629',
                'name' => 'MAGYAR ESL. DOB. TEJ2,8%1L',
                'unit_price' => 629.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '99',
                'name' => 'SZENDVICS VEKNI 3506',
                'unit_price' => 99.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '99',
                'name' => 'SPAR CSASZARZSEMLE (KF)',
                'unit_price' => 99.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => 'Ft',
                'name' => 'SPAR CSASZARZSEMLE (KF) K',
                'unit_price' => 0.0,
            ],
        ],
    ];

    const SAMPLE_07 = 'Esamin Hunoery Kft.
1133 BUDAPEST
POZSONYT UT 48 FOZ et 2 aitd

J
1085 Budapest
Jozsef kérut 50
ADOSZAM: 26289201-2-41

_ Kish =

SZENSAV.. ASV. 2l. 109

COO S~BUDGET

SESE: fg et

FORINT 180. Ft
VISSZAJARO 40 Fr
KEREKLTES 1 Ft

JOO RIK DORK ORK IIR ICRI IR IKI EK IIE
KOSZONJUK A VASARLAST! WhW, SPAR. HU
A NETA KOTELEZETTSEG AZ ELADOT TERHELT
KOZV.SZOLG. ESETEN AZ AFA TV, 160, 8 SZERINT
JOO IRR IORI RIOR I TORR IK RK OKI RRR IRI I

TRSZ; 564031. PENZTAR: 102 PENZTAROS: 901012

NYUGTASZAM: 1497/0038?
2022.09.28. 18:29

NAV. Ellenor26 kod:E7598
APA03102703';

    const EXPECTED_07 = [
        'basketId' => '1497/0038?',
        'taxNumber' => '26289201-2-41',
        'marketAddress' => 'J 1085 Budapest Jozsef kérut 50',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYT UT 48 FOZ et 2 aitd',
        'companyName' => 'Esamin Hunoery Kft.',
        'date' => '2022.09.28. 18:29',
        'total' => '',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'name' => 'S~BUDGET',
                'price' => 'et',
                'unit_price' => 0.0,
            ],
        ],
    ];

    const SAMPLE_08 = 'SPAR Haavarorszéa Kereskedelni KFT.

2060 Bicske
SPAR ut 0326/1 Hrsz.

2) Supernarket
1083 Budapest
Szigony utca 12
ADOSZAM: 10485824-2-07

COO KOT.SERTESSULT F.S0 306 339
AQO PREMIUM S.KAR.4 SZEL.TC.

0,311 KG x 3 099 Ft/KG 964
AOD RG.S. KAR.CSN. 4 SZEL.1C

0.463 KG x 2 499 Ft/KG 1 10/7
COO S-BUDGET BURGONYA 2kG 738
COO HAGYMA VOROSHAGYMA KG

0.59 KG x 319 Ft/KG 188
COO SPAR LEBOMLO ZACSKO 1b
COO SPAR PARIZSI 2006 PP 918
COO KEMENCES SERT. SONKA 90G 999
COO SPAR CSEMEGE KARAJ 100G 049
BOO SPAR IR VAJ 2006 1 169
BOO SACHER SZELET 2506 1 129
BOO TORNYOS KOKUSZGOLYO 3009 1 190
COO AIRWAVES MENT-EU.DR. 146 249

OSE 6

BANKKARTYA 8 804 Ft

KXKKKKKKKX HUSEGPONT INFORMACIO xxxxxxxxx
OSSZES HUSEGPONT : 8
SRR IKK ROK KKK OR ROK KOK KR OK KKK KOR KOR KK ARK OK KK KK
KOSZONJUK A VASARLAST! WhW.SPAR.HU
A NETA KOTELEZETTSEG AZ ELADOT TERHELT
KOZV.SZOLG. ESETEN AZ AFA TV. 160. 8 SZERINT
OOK KK RRR KIRK KR RK KR IKK KKK RRA KIRK K RR IKK RK KK
Sporoli még tobbet egyszertien a
MYSPAR APP-PAL! Toltsd le most!
A minédség elismerése: mar 13. alkalommal
SUPERBRANDS-di jas a SPAR

IRI KKK KKK IK KK KOK IKK ROK IK IK KOK OK KK KKK KKK KKK KAA K

TRSZ: 1667323 PENZTAR: 103 PENZTAROS: 217991

NYUGTASZAM: 2138/00055
2022.10.18. 12:42

NAV Ellenérz6 kod:33305
#PA03101500';

    const EXPECTED_08 = [
        'basketId' => '2138/00055',
        'taxNumber' => '10485824-2-07',
        'marketAddress' => '2) Supernarket 1083 Budapest Szigony utca 12',
        'marketName' => '',
        'companyAddress' => '2060 Bicske SPAR ut 0326/1 Hrsz.',
        'companyName' => 'SPAR Haavarorszéa Kereskedelni KFT.',
        'date' => '2022.10.18. 12:42',
        'total' => ' 8 804',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '339',
                'name' => 'KOT.SERTESSULT F.S0 306',
                'unit_price' => 339.0,
            ],
            [
                'quantity' => 0.311,
                'quantity_unit_id' => 1,
                'price' => '964',
                'name' => 'PREMIUM S.KAR.4 SZEL.TC.',
                'unit_price' => 3099.67845659164,
            ],
            [
                'quantity' => 0.463,
                'quantity_unit_id' => 1,
                'price' => '10/7',
                'name' => 'RG.S. KAR.CSN. 4 SZEL.1C',
                'unit_price' => 21.59827213822894,

            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '738',
                'name' => 'S-BUDGET BURGONYA 2kG',
                'unit_price' => 738.0,
            ],
            [
                'quantity' => 0.59,
                'quantity_unit_id' => 1,
                'price' => '188',
                'name' => 'HAGYMA VOROSHAGYMA KG',
                'unit_price' => 318.64406779661016,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1',
                'name' => 'SPAR LEBOMLO ZACSKO',
                'unit_price' => 1.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '918',
                'name' => 'SPAR PARIZSI 2006 PP',
                'unit_price' => 918.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '999',
                'name' => 'KEMENCES SERT. SONKA 90G',
                'unit_price' => 999.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '049',
                'name' => 'SPAR CSEMEGE KARAJ 100G',
                'unit_price' => 49.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1169',
                'name' => 'SPAR IR VAJ 2006',
                'unit_price' => 1169.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1129',
                'name' => 'SACHER SZELET 2506',
                'unit_price' => 1129.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1190',
                'name' => 'TORNYOS KOKUSZGOLYO 3009',
                'unit_price' => 1190.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '249',
                'name' => 'AIRWAVES MENT-EU.DR. 146',
                'unit_price' => 249.0,
            ],
        ],
    ];

    const SAMPLE_09 = 'Esmin Hungary Kit.
1133 BUDAPEST
POZSONYT UT 46 FSZ em. 2 aito

1085 Budapest
Jozsef korut 50
ADOSZAM: 26289201-2-41

BOO RISTORANTE DIAVOLA 350G 1 340
COO CHIO CH.HAGYMA-TEJF. 1406 789
BOO MAGVAS VIDEKI CIPO SOOG 909
COO 3BIT SZELET 466 204
COO 3BIT SZELET 466 204
COO QUNAKAVICS DRAZSE 706 274
BOO OUBLIN DA.1R VAJ 2006 1 339
COO SPAR CSEM.UB 6-9CM 350G 419
COO SUT6ZACSKO 8DB-0S 314
(SSE: 0 ald Ft
BANKKARTYA 5 See Ft

KKKKKK HUSEGPONT INFORMAGIO  xxxxxxx%%
QSSZES HUSEGPONT : 9
HK IKI III IIR IR KER IER II RRB BRE R RI
KOSZONJUK A VASARLAST! Wilt. SPAR. HU
A NETA KOTELEZETTSEG AZ ELADOT TERHELT
KOZV.SZOLG, ESETEN AZ AFA TV. 160. § SZERINT
JOO OI OKIE R RAIA

TRS7: 719829 PENZTAR: 103 PENZTAROS: 901421

NYUGTASZAM: 1538/00763
2022.11.11. 18:10

NAV Ellenorz6 kod: DOA3A
APA03102704';

    const EXPECTED_09 = [
        'basketId' => '1538/00763',
        'taxNumber' => '26289201-2-41',
        'marketAddress' => '1085 Budapest Jozsef korut 50',
        'marketName' => '',
        'companyAddress' => '1133 BUDAPEST POZSONYT UT 46 FSZ em. 2 aito',
        'companyName' => 'Esmin Hungary Kit.',
        'date' => '2022.11.11. 18:10',
        'total' => ' 5 See',
        'items' => [
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1340',
                'name' => 'RISTORANTE DIAVOLA 350G',
                'unit_price' => 1340.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '789',
                'name' => 'CHIO CH.HAGYMA-TEJF. 1406',
                'unit_price' => 789.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '909',
                'name' => 'MAGVAS VIDEKI CIPO SOOG',
                'unit_price' => 909.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '204',
                'name' => '3BIT SZELET 466',
                'unit_price' => 204.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '274',
                'name' => 'QUNAKAVICS DRAZSE 706',
                'unit_price' => 274.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '1339',
                'name' => 'OUBLIN DA.1R VAJ 2006',
                'unit_price' => 1339.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '419',
                'name' => 'SPAR CSEM.UB 6-9CM 350G',
                'unit_price' => 419.0,
            ],
            [
                'quantity' => 1,
                'quantity_unit_id' => 3,
                'price' => '314',
                'name' => 'SUT6ZACSKO 8DB-0S',
                'unit_price' => 314.0,
            ],
        ],
    ];

    /*
     * Sample text provider
     * */
    public function sampleTextProvider()
    {
        return [
            'Sample 01' => [self::SAMPLE_01, self::EXPECTED_01],
            'Sample 03' => [self::SAMPLE_02, self::EXPECTED_02],
            'Sample 04' => [self::SAMPLE_03, self::EXPECTED_03],
            'Sample 05' => [self::SAMPLE_04, self::EXPECTED_04],
            'Sample 06' => [self::SAMPLE_05, self::EXPECTED_05],
            'Sample 10' => [self::SAMPLE_06, self::EXPECTED_06],
            'Sample 15 - cash payment' => [self::SAMPLE_07, self::EXPECTED_07],
            'Sample 25 - other shop' => [self::SAMPLE_08, self::EXPECTED_08],
            'Sample 30' => [self::SAMPLE_09, self::EXPECTED_09],
        ];
    }

    /**
     * @dataProvider sampleTextProvider
     */
    public function testParse($rawText, $extractedBasket)
    {
        $parser = new SparParserService();
        $result = $parser->parse($rawText);
        $this->assertEquals($extractedBasket, $result->toArray());
    }
}
