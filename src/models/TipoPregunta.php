<?php

class TipoPregunta extends Diapositiva
{
    private ?string $titulo;
    private ?string $pregunta;
    private ?string $respuestaA;
    private ?string $respuestaB;
    private ?string $respuestaC;
    private ?string $respuestaD;
    private ?string $respuestaCorrecta;

    public function __construct(int|null $idDiapositiva, ?string $titulo, ?string $pregunta, ?string $respuestaA, ?string $respuestaB, ?string $respuestaC, ?string $respuestaD, ?string $respuestaCorrecta)
    {
        parent::__construct($idDiapositiva);
        $this->titulo = $titulo;
        $this->pregunta = $pregunta;
        $this->respuestaA = $respuestaA;
        $this->respuestaB = $respuestaB;
        $this->respuestaC = $respuestaC;
        $this->respuestaD = $respuestaD;
        $this->respuestaCorrecta = $respuestaCorrecta;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getPregunta(): ?string
    {
        return $this->pregunta;
    }

    public function setPregunta(?string $pregunta): void
    {
        $this->pregunta = $pregunta;
    }

    public function getRespuestaA(): ?string
    {
        return $this->respuestaA;
    }

    public function setRespuestaA(?string $respuestaA): void
    {
        $this->respuestaA = $respuestaA;
    }

    public function getRespuestaB(): ?string
    {
        return $this->respuestaB;
    }

    public function setRespuestaB(?string $respuestaB): void
    {
        $this->respuestaB = $respuestaB;
    }

    public function getRespuestaC(): ?string
    {
        return $this->respuestaC;
    }

    public function setRespuestaC(?string $respuestaC): void
    {
        $this->respuestaC = $respuestaC;
    }

    public function getRespuestaD(): ?string
    {
        return $this->respuestaD;
    }

    public function setRespuestaD(?string $respuestaD): void
    {
        $this->respuestaD = $respuestaD;
    }

    public function getRespuestaCorrecta(): ?string
    {
        return $this->respuestaCorrecta;
    }

    public function setRespuestaCorrecta(?string $respuestaCorrecta): void
    {
        $this->respuestaCorrecta = $respuestaCorrecta;
    }


    public function nuevaDiapositiva(PDO $conn, int $idPresentacion): void
    {
        $stmt = $conn->prepare("INSERT INTO diapositiva(presentacion_id, orden) VALUES (?, ?)");
        $stmt->bindParam(1, $idPresentacion);
        $stmt->bindParam(2, $this->orden);
        $stmt->execute();

        $idDiapositiva = $conn->lastInsertId();
        $this->setId($idDiapositiva);

        $stmt = $conn->prepare("INSERT INTO tipoPregunta(diapositiva_id, presentacion_id, titulo, pregunta, respuesta_a, respuesta_b, respuesta_c, respuesta_d, respuesta_correcta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $idDiapositiva);
        $stmt->bindParam(2, $idPresentacion);
        $stmt->bindParam(3, $this->titulo);
        $stmt->bindParam(4, $this->pregunta);
        $stmt->bindParam(5, $this->respuestaA);
        $stmt->bindParam(6, $this->respuestaB);
        $stmt->bindParam(7, $this->respuestaC);
        $stmt->bindParam(8, $this->respuestaD);
        $stmt->bindParam(9, $this->respuestaCorrecta);
        $stmt->execute();
    }

    public function actualizarDiapositiva(PDO $conn, int $idPresentacion): void
{
    $idDiapositiva = $this->getId();
    
    // Obtener la información actual de la diapositiva tipoPregunta
    $stmt = $conn->prepare("SELECT titulo, pregunta, respuesta_a, respuesta_b, respuesta_c, respuesta_d, respuesta_correcta FROM tipoPregunta WHERE presentacion_id = ? AND diapositiva_id = ?");
    $stmt->bindParam(1, $idPresentacion);
    $stmt->bindParam(2, $idDiapositiva);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Comparar y actualizar solo si la diapositiva existe
        if ($row['titulo'] !== $this->getTitulo() ||
            $row['pregunta'] !== $this->getPregunta() ||
            $row['respuesta_a'] !== $this->getRespuestaA() ||
            $row['respuesta_b'] !== $this->getRespuestaB() ||
            $row['respuesta_c'] !== $this->getRespuestaC() ||
            $row['respuesta_d'] !== $this->getRespuestaD() ||
            $row['respuesta_correcta'] !== $this->getRespuestaCorrecta()) {
            
            // Actualizar todos los campos si alguno ha cambiado
            $stmt = $conn->prepare("UPDATE tipoPregunta SET titulo = ?, pregunta = ?, respuesta_a = ?, respuesta_b = ?, respuesta_c = ?, respuesta_d = ?, respuesta_correcta = ? WHERE presentacion_id = ? AND diapositiva_id = ?");
            
            $stmt->bindParam(1, $this->titulo);
            $stmt->bindParam(2, $this->pregunta);
            $stmt->bindParam(3, $this->respuestaA);
            $stmt->bindParam(4, $this->respuestaB);
            $stmt->bindParam(5, $this->respuestaC);
            $stmt->bindParam(6, $this->respuestaD);
            $stmt->bindParam(7, $this->respuestaCorrecta);
            $stmt->bindParam(8, $idPresentacion);
            $stmt->bindParam(9, $idDiapositiva);
            $stmt->execute();
        }
    }
}


    public function getDiapositivaHTML(): string
{
    return '
    <div class="d-container" data-id="' . $this->getId() . '">
            <img src="../assets/icons/eliminar.svg" alt="Eliminar Diapositiva" id="imgEliminar"
                onclick="confirmDelete(event, this.closest(\'.d-container\'))">
        <input class="focus" type="text" form="data_p" value="' . $this->getTitulo() . '" autocomplete="off"
        name="d_titulo_' . $this->getId() . '" placeholder="Haz click para añadir un título..." />
        <textarea id="textareaPregunta" class="focus" form="data_p" rows="4" maxlength="128" name="d_pregunta_' . $this->getId() . '" placeholder="Introduce tu pregunta:">' . $this->getPregunta() . '</textarea>
        <div class="respuestas">
            <div class="respuesta">
                <input type="text" form="data_p" name="d_respuesta_a_' . $this->getId() . '" placeholder="Respuesta A..." value="' . $this->getRespuestaA() . '" />
                <input type="text" form="data_p" name="d_respuesta_b_' . $this->getId() . '" placeholder="Respuesta B..." value="' . $this->getRespuestaB() . '" />
                <input type="text" form="data_p" name="d_respuesta_c_' . $this->getId() . '" placeholder="Respuesta C..." value="' . $this->getRespuestaC() . '" />
                <input type="text" form="data_p" name="d_respuesta_d_' . $this->getId() . '" placeholder="Respuesta D..." value="' . $this->getRespuestaD() . '" />
            </div>
            <div class="respuestaCorrecta">
                <p>Respuesta correcta:</p>
                <select form="data_p" name="d_respuesta_correcta_' . $this->getId() . '">
                    <option value="A" ' . ($this->getRespuestaCorrecta() === 'A' ? 'selected' : '') . '>A</option>
                    <option value="B" ' . ($this->getRespuestaCorrecta() === 'B' ? 'selected' : '') . '>B</option>
                    <option value="C" ' . ($this->getRespuestaCorrecta() === 'C' ? 'selected' : '') . '>C</option>
                    <option value="D" ' . ($this->getRespuestaCorrecta() === 'D' ? 'selected' : '') . '>D</option>
                </select>
            </div>
        </div>
    </div>';
}


    public function getDiapositivaPreview(): string
    {
        return '
        <div class="d-container" style="display: none;">
            <h2 class="d_titulo_' . $this->getId() . '" rp='. $this->getRespuestaCorrecta() .'>' . $this->getTitulo() . '</h2>
            <span class="d_pregunta">' . $this->getPregunta() . '</span>
            <div class="pregunta">
                <span>A: ' . $this->getRespuestaA() . '</span>
                <input type="radio" name="pregunta1" value="a">
            </div>
            <div class="pregunta">
                <span>B: ' . $this->getRespuestaB() . '</span>
                <input type="radio" name="pregunta1" value="b">
            </div>
            <div class="pregunta">
                <span>C: ' . $this->getRespuestaC() . '</span>
                <input type="radio" name="pregunta1" value="c">
            </div>
            <div class="pregunta">
                <span>D: ' . $this->getRespuestaD() . '</span>
                <input type="radio" name="pregunta1" value="d">
            </div>
            <button onclick="checkAnswer(event)">Responder</button>
        </div>';
    }

    public function getMiniatura(): string
    {
        return '
        <div class="d-miniatura" id_diapositiva="'. $this->getId() .'">
            <h1>' . $this->getTitulo() . '</h1>
            <h2>'. $this->getPregunta() .'</h2>
        </div>';
    }
}