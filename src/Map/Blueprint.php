<?php

namespace Nuwber\Oponka\Map;

use Closure;
use Illuminate\Support\Fluent;
use Nuwber\Oponka\Connection;

class Blueprint
{
    /**
     * The fields that should be mapped.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The commands that should be run for the type.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Blueprint constructor.
     */
    public function __construct(protected string $type, Closure $callback = null, protected ?string $index= null)
    {
        if (!is_null($callback)) {
            $callback($this);
        }
    }

    /**
     * Execute the blueprint against the database.
     *
     * @param Connection $connection
     * @param Grammar    $grammar
     *
     * @return array
     */
    public function build(Connection $connection, Grammar $grammar): array
    {
        $statement = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                $this->type => [
                    '_source'    => [
                        'enabled' => true,
                    ],
                    'properties' => $this->toDSL($grammar),
                ],
            ],
        ];

        return $connection->mapStatement($statement);
    }

    /**
     * Indicate that the table needs to be created.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function create(): Fluent
    {
        return $this->addCommand('create');
    }

    /**
     * Add a string field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function string(string $field, array $attributes = []): Fluent
    {
        return $this->addField('string', $field, $attributes);
    }

    /**
     * Add a date field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function date(string $field, array $attributes = []): Fluent
    {
        return $this->addField('date', $field, $attributes);
    }

    /**
     * Add a long numeric field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function long(string $field, array $attributes = []): Fluent
    {
        return $this->addField('long', $field, $attributes);
    }

    /**
     * Add an integer field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function integer(string $field, array $attributes = []): Fluent
    {
        return $this->addField('integer', $field, $attributes);
    }

    /**
     * Add a short numeric field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function short(string $field, array $attributes = []): Fluent
    {
        return $this->addField('short', $field, $attributes);
    }

    /**
     * Add a byte numeric field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function byte(string $field, array $attributes = []): Fluent
    {
        return $this->addField('byte', $field, $attributes);
    }

    /**
     * Add a double field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function double(string $field, array $attributes = []): Fluent
    {
        return $this->addField('double', $field, $attributes);
    }

    /**
     * Add a binary field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function binary(string $field, array $attributes = []): Fluent
    {
        return $this->addField('binary', $field, $attributes);
    }

    /**
     * Add a float field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function float(string $field, array $attributes = []): Fluent
    {
        return $this->addField('float', $field, $attributes);
    }

    /**
     * Add a boolean field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function boolean(string $field, array $attributes = []): Fluent
    {
        return $this->addField('boolean', $field, $attributes);
    }

    /**
     * Add a geo point field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function point(string $field, array $attributes = []): Fluent
    {
        return $this->addField('point', $field, $attributes);
    }

    /**
     * Add a geo shape field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function shape(string $field, array $attributes = []): Fluent
    {
        return $this->addField('shape', $field, $attributes);
    }

    /**
     * Add an IPv4 field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function ip(string $field, array $attributes = []): Fluent
    {
        return $this->addField('ip', $field, $attributes);
    }

    /**
     * Add a completion field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function completion(string $field, array $attributes = []): Fluent
    {
        return $this->addField('completion', $field, $attributes);
    }

    /**
     * Add a completion field to the map.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return Fluent
     */
    public function tokenCount(string $field, array $attributes = []): Fluent
    {
        return $this->addField('token_count', $field, $attributes);
    }

    /**
     * Add a nested map.
     *
     * @param string $field
     * @param Closure $callback
     *
     * @return Fluent
     */
    public function nested(string $field, Closure $callback): Fluent
    {
        return $this->addField('nested', $field, ['callback' => $callback]);
    }

    /**
     * Add a object map.
     *
     * @param string $field
     * @param Closure $callback
     *
     * @return Fluent
     */
    public function object(string $field, Closure $callback): Fluent
    {
        return $this->addField('object', $field, ['callback' => $callback]);
    }

    /**
     * Add a new field to the blueprint.
     *
     * @param string $type
     * @param string $name
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function addField(string $type, string $name, array $attributes = []): Fluent
    {
        $attributes = array_merge(compact('type', 'name'), $attributes);

        $this->fields[] = $field = new Fluent($attributes);

        return $field;
    }

    /**
     * Get the registered fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the command fields.
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->fields;
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function addCommand(string $name, array $parameters = []): Fluent
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand(string $name, array $parameters = []): Fluent
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

    /**
     * Get the raw DSL statements for the blueprint.
     *
     * @param Grammar $grammar
     *
     * @return array
     */
    public function toDSL(Grammar $grammar): array
    {
        $statements = [];

        // Each type of command has a corresponding compiler function on the schema
        // grammar which is used to build the necessary DSL statements to build
        // the blueprint element, so we'll just call that compilers function.
        foreach ($this->commands as $command) {
            $method = 'compile' . ucfirst((string) $command->name);

            if (method_exists($grammar, $method)) {
                if (!is_null($dsl = $grammar->$method($this, $command))) {
                    $statements = array_merge($statements, (array) $dsl);
                }
            }
        }

        return $statements;
    }
}
