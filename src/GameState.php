<?php 

final class GameState
{
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';
    const DIRECTION_RIGHT = 'right';
    const DIRECTION_LEFT = 'left';
    public $score = 1;
    public $direction = self::DIRECTION_RIGHT;
  public function __construct(
    int $maxX,
    int $maxY
  ) {
    $this->maxX = $maxX;
    $this->maxY = $maxY;

    $this->snake = [
        $this->craftRandomCoords(),
    ];

    $this->fruit = $this->craftRandomCoords();
  }

  private function incrementBody(): void
{
  $newHead = $this->snake[0];

  // Adjusts head direction
  switch ($this->direction) {
    case self::DIRECTION_UP:
      $newHead['y']--;
      break;
    case self::DIRECTION_DOWN:
      $newHead['y']++;
      break;
    case self::DIRECTION_RIGHT:
      $newHead['x']++;
      break;
    case self::DIRECTION_LEFT:
      $newHead['x']--;
      break;
  }

  // Adds new head, in front
  // of the whole the body
  $this->snake = array_merge(
    [$newHead],
    $this->snake
  );
}

public function score(): void
{
  $this->score++;
  $this->incrementBody();
  $this->fruit = $this->craftRandomCoords();
}


public function craftRandomCoords(): array
{
    return [
        'x' => random_int(0,9),
        'y' => random_int(0,9)
    ];
}

public function step(): void
{
  $this->incrementBody();

  // Remove last element
  array_pop($this->snake);

  // Warp body if necessary
  foreach ($this->snake as &$coords) {
    if ($coords['x'] > $this->maxX - 1) {
        $coords['x'] = 0;
    } else if ($coords['x'] < 0) {
        $coords['x'] = $this->maxX - 1;
    }

    if ($coords['y'] > $this->maxY - 1) {
        $coords['y'] = 0;
    } else if ($coords['y'] < 0) {
        $coords['y'] = $this->maxY - 1;
    }
  }
}
}