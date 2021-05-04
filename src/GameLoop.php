<?php
// GameLoop.php
use raylib\Collision;
use raylib\Color;
use raylib\Draw;
use raylib\Input\Key;
use raylib\Rectangle;
use raylib\Text;
use raylib\Timming;
use raylib\Vector2;
use raylib\Window;
use raylib\Image;

final class GameLoop
{
    const CELL_SIZE = 30;
    private $width;
    private $height;
    private $state;
    private $shouldStop = false;
    private $lastStep;
    private $texture;

    // ...
    public function __construct(
        int $width,
        int $height
    )
    {
        $this->width = $width;
        $this->height = $height;

        // 30
        $s = self::CELL_SIZE;
        $this->state = new GameState(
            (int)($this->width / $s),
            (int)($this->height / $s)
        );
    }

    public function start(): void
    {
        Window::init(
            $this->width,
            $this->height,
            'PHP Snake'
        );
        Timming::setTargetFPS(60);

        $image = new Image('resources/php-logo.png');    // Loaded in CPU memory (RAM)
        $this->texture = $image->toTexture();                                 // Image converted to texture, GPU memory (VRAM)
        unset($image);

        while (
            !$this->shouldStop and
            !Window::shouldClose()
        ) {
            $this->update();
            $this->draw();
        }

        Window::close();
    }

    private function update(): void
    {
        $head = $this->state->snake[0];
        $recSnake = new Rectangle(
            (float)$head['x'],
            (float)$head['y'],
            1,
            1,
        );

        $fruit = $this->state->fruit;
        $recFruit = new Rectangle(
            (float)$fruit['x'],
            (float)$fruit['y'],
            1,
            1,
        );

        // Snake bites fruit
        if (
        Collision::checkRecs(
            $recSnake,
            $recFruit
        )
        ) {
            $this->state->score();
        }

        // check collision with
        for ($i=2; $i < count($this->state->snake); $i++) {
            $snakePart = $this->state->snake[$i];
            $recSnakePart = new Rectangle(
                (float)$snakePart['x'],
                (float)$snakePart['y'],
                1,
                1,
            );

            // Snake bites fruit
            if (
            Collision::checkRecs(
                $recSnake,
                $recSnakePart
            )
            ) {
                $this->shouldStop = true;
            }
        }

        // Controls step speed
        $now = microtime(true);
        if (
            $now - $this->lastStep
            > (1 / $this->state->score)
        ) {
            $this->state->step();
            $this->lastStep = $now;
        }

        // Update direction if necessary
        if (Key::isPressed(Key::W)) {
            $this->state->direction = GameState::DIRECTION_UP;
        } else if (Key::isPressed(Key::D)) {
            $this->state->direction = GameState::DIRECTION_RIGHT;
        } else if (Key::isPressed(Key::S)) {
            $this->state->direction = GameState::DIRECTION_DOWN;
        } else if (Key::isPressed(Key::A)) {
            $this->state->direction = GameState::DIRECTION_LEFT;
        }
    }

    private function draw(): void
    {
        Draw::begin();

        // Clear screen
        Draw::clearBackground(
            new Color(255, 255, 255, 255)
        );

        // Draw fruit
        $x = $this->state->fruit['x'];
        $y = $this->state->fruit['y'];
        $white = new Color(255, 255, 255, 255);

        $this->texture->draw($x * self::CELL_SIZE, $y * self::CELL_SIZE, $white);

        // Draw snake's body
        foreach (
            $this->state->snake as $coords
        ) {
            $x = $coords['x'];
            $y = $coords['y'];
            Draw::rectangle(
                $x * self::CELL_SIZE,
                $y * self::CELL_SIZE,
                self::CELL_SIZE,
                self::CELL_SIZE,
                new Color(0, 255, 0, 255)
            );
        }

        // Draw score
        $score = "Score: {$this->state->score}";
        Text::draw(
            $score,
            $this->width - Text::measure($score, 12) - 10,
            10,
            12,
            new Color(0, 255, 0, 255)
        );

        Draw::end();
    }
    // ...
}