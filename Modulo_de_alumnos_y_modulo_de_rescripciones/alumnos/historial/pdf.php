<?php
require('fpdf/fpdf.php');

$fpdf = new FPDF();
$fpdf->AddPage('portrait', 'letter');
class PDF extends FPDF
{
// Cabecera de página
 public function Header()
{
    $this->Image('../../../primera_parte/estilos/imagenes/logo.png',10,0,40,40,'png');
    $this->SetFont('Arial','B',35);
    $this->Cell(0,15,utf8_decode('Historial académico'),0,0,'C');
    $this->Ln(10);
    
}

// Pie de página
public function Footer()
{
    $this->SetFont('Courier','I',12);
    $this->SetY(-15);
    $this->Write(5,'Documento oficial');
}
}


$fpdf = new pdf('P','mm','letter',true);
$fpdf->AddPage('portrait', 'letter');
$fpdf->SetFont('Arial','B',20);
$fpdf->SetY(40);
$fpdf->Cell(0, 25, utf8_decode('INFORMACIÓN DEL PLANTEL'),0,0,'C');

$fpdf->SetFont('Arial','B',15);
$fpdf->SetY(20);
$fpdf->Cell(0, 80, utf8_decode('Centro de Bachillerato Tecnológico Industrial y de Servicios 179'),0,0,'C');

$fpdf->SetFont('Arial','B',12);
$fpdf->SetY(20);
$fpdf->Cell(0, 95, utf8_decode('Calle Lic. Adolfo Lugo Verduzco No. 105, Col. La Cañada'),0,0,'C');
$fpdf->SetY(20);
$fpdf->Cell(0, 105, utf8_decode('Tulancingo de Bravo, Estado de Hidalgo, México
C.P. 43615'),0,0,'C');
$fpdf->SetY(20);
$fpdf->Cell(0, 115, utf8_decode('
Teléfonos:
01(775)7535520 y 01(775)7554420'),0,0,'C');


require 'cn.php';
session_start();
$curp = $_SESSION['curp'];
    $consulta = "SELECT * FROM alumnos where curp='$curp'";
    $resultado = $mysqli -> query($consulta);
        if (mysqli_num_rows($resultado) == 1) {
        $row = mysqli_fetch_array($resultado);
        $id_aspirante = $row['id_aspirante'];
        
    $fpdf->SetFont('Arial','BU',20);
    $fpdf->SetY(65);
    $fpdf->SetTextColor(0, 27, 105);
    $fpdf->Ln(30);
    $fpdf->Cell(0, 0, utf8_decode('INFORMACIÓN DEL ASPIRANTE'),0,0,'C');

    $fpdf->SetFont('Arial','',12);
    $fpdf->SetTextColor(0,0,0);
    $fpdf->Ln(6);
    

            $fpdf->SetFont('Arial','',12);
            $fpdf->SetX(50);
            $fpdf->Cell(22,10,'GRUPO:', 0,0,'L',0);
            $fpdf->SetFont('Arial','B',12);
            $fpdf->Cell(30,10,utf8_decode($row['grupo']), 0,0,'L',0);
        
            $fpdf->SetFont('Arial','',12);
            $fpdf->SetX(132);
                $fpdf->Cell(18,10,utf8_decode('FOLIO:'), 0,0,'L',0);
            $fpdf->SetFont('Arial','B',12);
            $fpdf->Cell(28,10,utf8_decode($row['id_aspirante']), 0,0,'L',0);
            $fpdf->Ln(8);
            $fpdf->SetX(72);
                $fpdf->Cell(18,0,'', 1,0,'L',0);
                $fpdf->SetX(148);
                $fpdf->Cell(22,0,'', 1,0,'L',0);

$fpdf->SetFont('Arial','',12);
$fpdf->SetTextColor(0,0,0);
$fpdf->Ln(1);
$fpdf->SetX(50);
    $fpdf->Cell(22,10,utf8_decode('NOMBRE:  '), 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(98,10,utf8_decode($row['nombre'].' '. $row['primer_apellido']. ' '. $row['segundo_apellido'] ), 0,0,'L',0);
    $fpdf->Ln(8);
$fpdf->SetX(72);
    $fpdf->Cell(98,0,'', 1,0,'L',0);



    $fpdf->SetFont('Arial','',12);
$fpdf->SetX(50);
    $fpdf->Cell(22,10,utf8_decode('CURP:  ') , 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(98,10,$row['curp'], 0,0,'L',0);
    $fpdf->Ln(8);
    $fpdf->SetX(72);
        $fpdf->Cell(98,0,'', 1,0,'L',0);

    $fpdf->SetFont('Arial','',12);
$fpdf->SetX(50);
    $fpdf->Cell(55,10,'FECHA DE NACIMIENTO:  ', 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(6,10,utf8_decode($row['fecha_nacimiento']), 0,0,'L',0);
    $fpdf->Ln(8);
    $fpdf->SetX(105);
        $fpdf->Cell(65,0,'', 1,0,'L',0);

    $fpdf->SetFont('Arial','',12);
$fpdf->SetX(50);
    $fpdf->Cell(25,10,'CORREO:  ', 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(100,10,utf8_decode($row['correo_electronico']), 0,0,'L',0);
    $fpdf->Ln(8);
    $fpdf->SetX(75);
        $fpdf->Cell(95,0,'', 1,0,'L',0);


if ($row['genero'] == "masculino") {
    $fpdf->SetFont('Arial','',12);
    $fpdf->SetX(50);
    $fpdf->Cell(22,10,'GENERO:  ', 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(30,10,"MASCULINO", 0,0,'L',0);

    $fpdf->SetFont('Arial','',12);
    $fpdf->SetX(102);
        $fpdf->Cell(26,10,utf8_decode('TELÉFONO:'), 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(28,10,$row['telefono'], 0,0,'L',0);
    $fpdf->Ln(8);
    $fpdf->SetX(72);
        $fpdf->Cell(28,0,'', 1,0,'L',0);
        $fpdf->SetX(128);
        $fpdf->Cell(42,0,'', 1,0,'L',0);
}else{
    $fpdf->SetFont('Arial','',12);
    $fpdf->SetX(50);
    $fpdf->Cell(22,10,'GENERO:  ', 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(30,10,"  MUJER", 0,0,'L',0);

    $fpdf->SetFont('Arial','',12);
    $fpdf->SetX(102);
        $fpdf->Cell(26,10,utf8_decode('TELÉFONO:'), 0,0,'L',0);
    $fpdf->SetFont('Arial','B',12);
    $fpdf->Cell(28,10,$row['telefono'], 0,0,'L',0);
    $fpdf->Ln(8);
    $fpdf->SetX(72);
        $fpdf->Cell(28,0,'', 1,0,'L',0);
        $fpdf->SetX(128);
        $fpdf->Cell(42,0,'', 1,0,'L',0);
    }

}
$fpdf->SetFont('Arial','B',16);
    // Escribir título
$fpdf->SetY(65);
$fpdf->Ln(100);
$fpdf->Cell(0, 0, utf8_decode('CALIFICACIONES'),0,0,'C');

// Configurar fuente y tamaño para las calificaciones
$fpdf->SetFont('Arial','',12);

// Configurar posición para las calificaciones
$fpdf->Ln(6);
$fpdf->SetX(28);

// Dibujar la tabla de calificaciones
$fpdf->Cell(40, 10, 'Materia', 1, 0, 'C');
$fpdf->Cell(60, 10, 'Profesor', 1, 0, 'C');
$fpdf->Cell(10, 10, '1ro', 1, 0, 'C');
$fpdf->Cell(10, 10, '2do', 1, 0, 'C');
$fpdf->Cell(10, 10, '3ro', 1, 0, 'C');
$fpdf->Cell(30, 10, 'Status', 1, 1, 'C');

// Consulta para obtener la información de las calificaciones del alumno
$query_calificaciones = "SELECT calificaciones.*, materias.nombre AS materia, CONCAT(profesores.nombre, ' ', profesores.primer_apellido, ' ', profesores.segundo_apellido) AS profesor
                         FROM calificaciones
                         INNER JOIN materias ON calificaciones.id_materia = materias.id_materia
                         INNER JOIN profesores ON materias.id_profesor = profesores.id_profesor
                         WHERE calificaciones.id_aspirante = " . $row['id_aspirante'];

$resultado_calificaciones = mysqli_query($mysqli, $query_calificaciones);

// Iterar sobre las calificaciones y dibujarlas en el PDF
while ($fila = mysqli_fetch_assoc($resultado_calificaciones)) {
    $fpdf->SetX(28);
    $fpdf->Cell(40, 10, utf8_decode($fila['materia']), 1, 0, 'C');
    $fpdf->Cell(60, 10, utf8_decode($fila['profesor']), 1, 0, 'C');
    $fpdf->Cell(10, 10, $fila['primer_parcial'], 1, 0, 'C');
    $fpdf->Cell(10, 10, $fila['segundo_parcial'], 1, 0, 'C');
    $fpdf->Cell(10, 10, $fila['tercer_parcial'], 1, 0, 'C');
    $fpdf->Cell(30, 10, $fila['estado_parcial'], 1, 1, 'C');
}

// Agregar espacio para las firmas
$fpdf->SetY(240);
$fpdf->SetX(45);
$fpdf->Cell(50,0,'', 1,0,'L',0);
$fpdf->SetY(245);
$fpdf->SetX(45);
$fpdf->Cell(50,0,'Firma del alumno', 0,0,'C',0);

$fpdf->SetY(240);
$fpdf->SetX(110);
$fpdf->Cell(66,0,'', 1,0,'L',0);
$fpdf->SetY(245);
$fpdf->SetX(110);
$fpdf->Cell(66,0,utf8_decode('Firma de padre/tutor'), 0,0,'C',0);

// Salida del PDF
$fpdf->Output('I',utf8_decode('historial_académico.pdf'));  