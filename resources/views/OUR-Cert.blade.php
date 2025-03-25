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
            padding-top: 2in;
        }

        .title {
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
            line-height: 1.2;
            margin-bottom: 20px;
            text-indent: 50px;
            font-size: 12pt;
        }
        .content-issued {
            text-align: justify;
            line-height: 1.2;
            margin-bottom: 40px;
            text-indent: 50px;
            font-size: 12pt;
        }


        .signature {
            margin-top: 80px;
            margin-right: 0;
            font-size: 12pt;
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
            position: absolute;
            bottom: 50px;
            left: 1.25in;
            font-size: 9pt;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <div class="title">C E R T I F I C A T I O N</div>

        <div class="concern">TO WHOM IT MAY CONCERN:</div>

        <div class="content">

            This is to certify that <strong>
            @if($student->sex == 'M')
            Mr.
            @else
            Ms.
            @endif
            {{ $student->first_name }} {{ $student->middle_name ? $student->middle_name : '' }} {{ $student->last_name }} {{ $student->suffix ?? '' }}</strong>
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
            has graduated with the degree of <strong>{{ $student->records()->first()?->curricula?->programs?->program_name }} ({{ $student->records()->first()?->curricula?->programs?->program_abbreviation }}){{($honor = $student->graduationInfos->first()?->latin_honor) ?", $honor" : '' }}
            </strong>from {{ $collegeName }}, {{ $campus }} on {{ date('F j, Y', strtotime($student->graduationInfos->first()->graduation_date)) }}
            per Referendum No. {{ $student->graduationInfos->first()->board_approval }}, of the Board of Regents, Bicol University.
        </div>
        <div class="content">
            It is further certified that the Bicol University is a government institution operating
            under R.A. No. 5521, as amended, whose graduates are <strong>not issued a Special Order</strong>.
        </div>
        <div class="content">
            This certifies finally that the degree of <strong>{{ $student->records()->first()?->curricula?->programs?->program_name }} ({{ $student->records()->first()?->curricula?->programs?->program_abbreviation }})</strong> course is duly approved by the Board of Regents, Bicol University,
            Legazpi City.
        </div>
        <div class="content-issued">
            Issued this {{ now()->format('jS') }} day of {{ now()->format('F, Y') }} upon the request of the interested party for
            reference purposes.
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

        <div class="footer">
            BU-F-UREG-07<br>
            Effectivity Date: Mar. 9, 2011<br>
            <span style="text-align: right;">Revision: 1</span>
        </div>
    </div>
</body>

</html>