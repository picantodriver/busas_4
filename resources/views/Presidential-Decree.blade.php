<?php

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certification</title>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }
        body {
            font-family: Times New Roman, serif;
            width: 8.5in;
            height: 11in;
            position: relative;
            margin: 0;
            padding: 0;
        }
        .certificate {
            width: 6in;
            margin: 0 auto;
            /* padding-top: 2in; */
        }
        .title {
            padding-top: 4.5cm;
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 60px;
        }
        .concern {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
            line-height: 1.5;
            margin-bottom: 20px;
            text-indent: 50px;
            font-size: 12pt;
        }
        .signature {
            margin-top: 80px;
            margin-right: 0;
            font-size: 12pt;
            line-height: 15px;
        }
        .registrar-name {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12pt;
        }
        .registrar-title {
            margin-top: 0;
        }
        .footer {
            /* position: absolute; */
            padding-top: 4in;
            bottom: 50px;
            left: 1.25in;
            font-size: 9pt;
        }
        /* .footer_right {
            float: right;
        } */
    </style>
</head>

<body>
    <div class="certificate">
        <div class="title">C E R T I F I C A T I O N</div>

        <div class="concern">TO WHOM IT MAY CONCERN:</div>

        <div class="content">

            This is to certify that<strong>
                {{ strtoupper($student->first_name) }} {{ strtoupper($student->middle_name ?? '') }} {{ strtoupper($student->last_name) }} {{ strtoupper($student->suffix ?? '') }}</strong>    
            @php
            $record = $student->records()->first();
            $campus = 'N/A';
            $collegeName = '';

            if ($record && $record->college) {
            $campusName = $record->campus->campus_name ?? '';

            if (in_array($campusName, ['Main Campus', 'East Campus', 'Daraga Campus'])) {
            $collegeName = 'Bicol University ' . $record->college->college_name;
                switch ($campusName) {
                case 'Main Campus':
                $campus = 'Main Campus, Legazpi City';
                break;
                case 'East Campus':
                $campus = 'East Campus, Legazpi City';
                break;
                case 'Daraga Campus':
                $campus = 'Daraga Campus, Daraga, Albay';
                break;
                }
                } else {
                $collegeName = $record->college->college_name;
                    switch ($record->college->college_name) {
                    case 'Bicol University Gubat':
                    $campus = 'Gubat, Sorsogon';
                    break;
                    case 'Bicol University Guinobatan':
                    $campus = 'Guinobatan, Albay';
                    break;
                    case 'Bicol University Polangui':
                    $campus = 'Polangui, Albay';
                    break;
                    case 'Bicol University Tabaco':
                    $campus = 'Tabaco City, Albay';
                    break;
                    }
                }
            }
            @endphp
            has graduated with the degree of <strong>{{ $student->records()->first()?->curricula?->programs?->program_name }} ({{ $student->records()->first()?->curricula?->programs?->program_abbreviation}}){{($honor = $student->graduationInfos->first()?->latin_honor) ?", $honor" : '' }} </strong>from {{ $collegeName }}, {{ $campus }} on {{ date('F j, Y', strtotime($student->graduationInfos->first()->graduation_date)) }}
            per Referendum No. {{ $student->graduationInfos->first()->board_approval }} of the Board of Regents,
            having a <strong>General Weighted Average</strong> (GWA) of <strong>{{ $student->gwa }}</strong>
            @if($student->gwa == 1.0000)
                <strong>(100%).</strong>
            @elseif($student->gwa >= 1.0001 && $student->gwa <= 1.0999)
                <strong>(99%).</strong>
            @elseif($student->gwa >= 1.1000 && $student->gwa <= 1.1999)
                <strong>(98%).</strong>
            @elseif($student->gwa >= 1.2000 && $student->gwa <= 1.2999)
                <strong>(97%).</strong>
            @elseif($student->gwa >= 1.3000 && $student->gwa <= 1.3999)
                <strong>(96%).</strong>
            @elseif($student->gwa >= 1.4000 && $student->gwa <= 1.4999)
                <strong>(95%).</strong>
            @elseif($student->gwa >= 1.5000 && $student->gwa <= 1.5999)
                <strong>(94%).</strong>
            @elseif($student->gwa >= 1.6000 && $student->gwa <= 1.6999)
                <strong>(93%).</strong>
            @elseif($student->gwa >= 1.7000 && $student->gwa <= 1.7999)
                <strong>(92%).</strong>
            @elseif($student->gwa >= 1.8000 && $student->gwa <= 1.8999)
                <strong>(91%).</strong>
            @elseif($student->gwa >= 1.9000 && $student->gwa <= 1.9999)
                <strong>(90%).</strong>
            @elseif($student->gwa >= 2.0000 && $student->gwa <= 2.0999)
                <strong>(89%).</strong>
            @elseif($student->gwa >= 2.1000 && $student->gwa <= 2.1999)
                <strong>(88%).</strong>
            @elseif($student->gwa >= 2.2000 && $student->gwa <= 2.2999)
                <strong>(87%).</strong>
            @elseif($student->gwa >= 2.3000 && $student->gwa <= 2.3999)
                <strong>(86%).</strong>
            @elseif($student->gwa >= 2.4000 && $student->gwa <= 2.4999)
                <strong>(85%).</strong>
            @elseif($student->gwa >= 2.5000 && $student->gwa <= 2.5999)
                <strong>(84%).</strong>
            @elseif($student->gwa >= 2.6000 && $student->gwa <= 2.6499)
                <strong>(83%).</strong>
            @elseif($student->gwa >= 2.6500 && $student->gwa <= 2.6999)
                <strong>(82%).</strong>
            @elseif($student->gwa >= 2.7000 && $student->gwa <= 2.7499)
                <strong>(81%).</strong>
            @elseif($student->gwa >= 2.7500 && $student->gwa <= 2.7999)
                <strong>(80%).</strong>
            @elseif($student->gwa >= 2.8000 && $student->gwa <= 2.8499)
                <strong>(79%).</strong>
            @elseif($student->gwa >= 2.8500 && $student->gwa <= 2.8999)
                <strong>(78%).</strong>
            @elseif($student->gwa >= 2.9000 && $student->gwa <= 2.9499)
                <strong>(77%).</strong>
            @elseif($student->gwa >= 2.9500 && $student->gwa <= 2.9999)
                <strong>(76%).</strong>
            @elseif($student->gwa >= 3.0000)
                <strong>(75%).</strong>
            @endif
        </div>

        <div class="content">
            Issued this {{ now()->format('j') }}<sup>{{ now()->format('S') }}</sup> day of {{ now()->format('F, Y') }} upon the request of the interested party for
            PD 907 purposes.
        </div>

        <div class="signature">
            @php
            $certifiedBy = App\Models\Signatories::where('employee_designation', 'University Registrar')
            ->first();
            @endphp
            <div style="text-align: center; width: 215px; float: right;">
                <div class="registrar-name">{{ strtoupper($certifiedBy->employee_name) }}{{ $certifiedBy->suffix ? ', ' . $certifiedBy->suffix : '' }}</div>
                <div class="registrar-title">{{ $certifiedBy->employee_designation }}</div>
            </div>
        </div>

        <div class="footer" style="font-family: BerlinSans;">
            <div class="footer_left" style="float: left; text-align: left;"> 
                <strong>BU-F-UREG-05</strong><br>
                <strong>Effectivity Date: Mar. 9, 2011</strong><br>
            </div>
            <div class="footer_right" style="float: right; text-align: right;">
            {{-- @php
            $initials = App\Models\User::find(auth()->id())->initials ?? '';
            @endphp --}}
            <span><strong>Revision: 1</strong><br></span>
            {{-- <span>/{{ $initials }}</span> --}}
            </div>
        </div>
    </div>
</body>

</html>