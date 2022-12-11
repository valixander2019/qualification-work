<?php


namespace App\Library;


use App\Library\Fraction\Fraction;
use App\Library\Traits\Metable;
use App\Library\Traits\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Handler implements \JsonSerializable
{
    use Metable;
    use Utils;

    /**
     * Идентификатор
     *
     * @var int
     */
    private int $id;

    /**
     * Исполняемый код
     *
     * @var string
     */
    private string $code;

    /**
     * Формула по умолчанию
     *
     * @var string
     */
    private string $defaultFormula = '';

    /**
     * Формула
     *
     * @var string
     */
    private string $formula = '';

    /**
     * Поля
     *
     * @var array
     */
    private array $fields = [];

    /**
     * Ответы
     *
     * @var array
     */
    private array $answers = [];

    /**
     * Порядок
     *
     * @var bool
     */
    private bool $order = false;

    /**
     * Параметры
     *
     * @var array
     */
    private array $params = [];

    /**
     * Найти или сгенерировать обработчик
     *
     * @param  int  $id
     * @param  string  $code
     *
     * @return void
     */
    public function __construct(int $id, string $code)
    {
        $this->setId($id);
        $this->setCode($code);
    }

    /**
     * Метод обработчика
     *
     * @param $code
     *
     * @return void
     */
    public function handle ($code): void
    {
        eval($code);
    }

    /**
     * Найти или сгенерировать обработчик
     *
     * @return $this
     */
    public function findOrGenerate (): self
    {
        if ($this->isGenerated()) {
            $handler = $this->findHandler();

            $this->fill(
                $handler->getFormula(),
                $handler->getFields(),
                $handler->getAnswers(),
                $handler->getParams(),
                $handler->getOrder()
            );
        } else {
            $this->generate();
        }

        return $this;
    }

    /**
     * Сгенерировать обработчик
     *
     * @return $this
     */
    public function generate (): self
    {
        $this->handle($this->getCode());
        $this->saveHandler();

        return $this;
    }

    /**
     * Проверить корректность ответов
     *
     * @param  array  $answers
     *
     * @return bool
     */
    public function check (array $answers): bool
    {
        if ($this->getOrder()) {
            foreach ($this->getFields() as $key => $field) {
                $correctAnswer = Arr::get($this->getAnswers(), $key);
                $answer = Arr::get($answers, $key);

                if (Arr::get($field, 'type') === 'fraction' || Arr::get($field, 'type') === 'mixed-fraction') {
                    if (Arr::get($correctAnswer, 0) !== Arr::get($answer, 0) || Arr::get($correctAnswer, 1) !== Arr::get($answer, 1) || Arr::get($correctAnswer, 2) !== Arr::get($answer, 2))
                        return false;
                } else {
                    if ($correctAnswer !== $answer)
                        return false;
                }
            }

            return true;
        } else {
            return empty(array_diff(array_map(function ($el) {
                return is_array($el) ? json_encode($el) : $el;
            }, $this->getAnswers()), array_map(function ($el) {
                return is_array($el) ? json_encode($el) : $el;
            }, $answers)));
        }
    }

    /**
     * Заполнить данными обработчик
     *
     * @param  string  $formula
     * @param  array  $fields
     * @param  array  $answers
     * @param  array  $params
     * @param  bool  $order
     *
     * @return $this
     */
    protected function fill (string $formula, array $fields, array $answers, array $params, bool $order): self
    {
        $this->setFormula($formula);
        $this->setFields($fields);
        $this->setAnswers($answers);
        $this->setParams($params);
        $this->setOrder($order);

        return $this;
    }

    /**
     * Проверить существование обработчика
     *
     * @return bool
     */
    private function isGenerated (): bool
    {
        return session()->exists($this->getSessionName());
        return false;
    }

    /**
     * Найти обработчик в сессии
     *
     * @return $this|null
     */
    private function findHandler (): ?self
    {
        if (!$this->isGenerated())
            return null;

        $data = session()->get($this->getSessionName());
        $handler = clone $this;

        return $handler->fill(
            Arr::get($data, 'formula'),
            Arr::get($data, 'fields'),
            Arr::get($data, 'answers'),
            Arr::get($data, 'params'),
            Arr::get($data, 'order')
        );
    }

    /**
     * Сохранить обработчик в сессию
     *
     * @return $this
     */
    private function saveHandler (): self
    {
        session()->put($this->getSessionName(), $this->jsonSerialize());
        session()->save();

        return $this;
    }

    /**
     * Получить идентификатор
     *
     * @return int
     */
    private function getId (): int
    {
        return $this->id;
    }

    /**
     * Установить идентификатор
     *
     * @return $this
     */
    private function setId (int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Получить код
     *
     * @return string
     */
    private function getCode (): string
    {
        return $this->code;
    }

    /**
     * Установить код
     *
     * @return $this
     */
    private function setCode (string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Получить формулу по умолчанию
     *
     * @return string
     */
    private function getDefaultFormula (): string
    {
        return $this->defaultFormula;
    }

    /**
     * Установить формулу по умолчанию
     *
     * @param  string  $defaultFormula
     *
     * @return $this
     */
    private function setDefaultFormula (string $defaultFormula): self
    {
        $this->defaultFormula = $defaultFormula;
        return $this;
    }

    /**
     * Получить поля
     *
     * @return array
     */
    private function getFields (): array
    {
        return $this->fields;
    }

    /**
     * Установить поля
     *
     * @param  array  $fields
     *
     * @return $this
     */
    private function setFields (array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Получить формулу
     *
     * @return string
     */
    private function getFormula (): string
    {
        return $this->formula;
    }

    /**
     * Установить формулу
     *
     * @param  string  $formula
     *
     * @return $this
     */
    private function setFormula (string $formula): self
    {
        $this->formula = $formula;
        return $this;
    }

    /**
     * Получить ответы
     *
     * @return array
     */
    private function getAnswers (): array
    {
        return $this->answers;
    }

    /**
     * Установить ответы
     *
     * @param  array  $answers
     *
     * @return $this
     */
    private function setAnswers (array $answers): self
    {
        $this->answers = $answers;
        return $this;
    }

    /**
     * Получить порядок
     *
     * @return bool
     */
    private function getOrder (): bool
    {
        return $this->order;
    }

    /**
     * Установить порядок
     *
     * @param  bool  $order
     *
     * @return $this
     */
    private function setOrder (bool $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Получить параметры
     *
     * @return array
     */
    private function getParams (): array
    {
        return $this->params;
    }

    /**
     * Установить параметры
     *
     * @param  array  $params
     *
     * @return $this
     */
    private function setParams (array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Получить имя сессии
     *
     * @return string
     */
    private function getSessionName (): string
    {
        return 'Task_' . $this->getId();
    }

    /**
     * Подготовить формулу
     *
     * @param  array  $params
     *
     * @return string
     */
    public function prepareFormula (array $params): string
    {
        $formula = $this->getDefaultFormula();

        for ($i = 0; $i < strlen($this->getDefaultFormula()) / 5; $i++) {
            foreach ($params as $k => $v) {
                $strpos = strpos($formula, $k);

                if ($strpos === false) continue;

                $nextSymbol = substr($formula, $strpos + strlen($k), 1);
                $prevSymbol = substr($formula, $strpos - 1, 1);
                $substr = substr($formula, $strpos - 1, strlen($k) + 1);

                if (is_array($v) && $v[1] === 1) {
                    $v = $v[0];
                }

                if (is_array($v)) {
                    $search = $substr;
                    $fracArr = $this->arr2fracArr($v);

                    if (count($fracArr) === 3) {
                        $replace = $fracArr[2] . '\frac{' . $fracArr[0] . '}{' . $fracArr[1] . '}';
                    } else {
                        $replace = '\frac{' . $fracArr[0] . '}{' . $fracArr[1] . '}';
                    }
                } else {
                    if (in_array($nextSymbol, array_merge(range('a', 'z'), range('A', 'Z')))) {
                        if (!in_array($prevSymbol, ['+', '-'])) {
                            $search = $k;

                            if (abs($v) === 1) {
                                $replace = $v === 1 ? '' : '-';
                            } else {
                                $replace = $v;
                            }
                        } else {
                            $search = $substr;

                            if (abs($v) === 1) {
                                $replace = $v === 1 ? '+' : '-';
                            } else {
                                $replace = $v > 0 ? '+' . abs($v) : '-' . abs($v);
                            }
                        }
                    } else {
                        if ($prevSymbol === '=') {
                            $search = $k;

                            if (abs($v) === 1) {
                                $replace = $v === 1 ? '' . abs($v) : '-' . abs($v);
                            } else {
                                $replace = $v;
                            }
                        } else {
                            $search = $substr;

                            if ($v > 0) {
                                $replace = $substr[0] . abs($v);
                            } else {
                                if ($substr[0] === '-') {
                                    $replace = '+' . abs($v);
                                } else {
                                    $replace = '-' . abs($v);
                                }
                            }
                        }
                    }
                }

                $formula = Str::of($formula)->replace($search, $replace);
            }
        }

        return $formula;
    }

    /**
     * Формула по умолчанию
     *
     * @param  string  $formula
     *
     * @return $this
     */
    public function formula (string $formula): self
    {
        $this->setDefaultFormula('$$ ' . $formula . ' $$');
        return $this;
    }

    /**
     * Поля
     *
     * @param  array  $fields
     * @param  bool  $order
     *
     * @return $this
     */
    public function fields (array $fields, bool $order = false): self
    {
        $this->setFields($fields);
        $this->setOrder($order);
        return $this;
    }

    /**
     * Создать задачу
     *
     * @param  array  $params
     * @param  array  $answers
     *
     * @return Handler
     */
    public function task (array $params, array $answers): self
    {
        return $this->fill($this->prepareFormula($params), $this->getFields(), array_values($answers), array_values($params), $this->getOrder());
    }

    /**
     * Подготовить для сериализации JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'id' => $this->getId(),
                'code' => $this->getCode(),
                'defaultFormula' => $this->getDefaultFormula(),
                'formula' => $this->getFormula(),
                'answers' => $this->getAnswers(),
                'order' => $this->getOrder(),
                'params' => $this->getParams(),
                'fields' => $this->getFields(),
            ],
            $this->meta()
        );
    }
}
