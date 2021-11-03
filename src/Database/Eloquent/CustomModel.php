<?php

namespace Cyrus\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use ArrayAccess;
use Illuminate\Contracts\Broadcasting\HasBroadcastChannel;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use LogicException;

abstract class TestCustomModel extends \Illuminate\Database\Eloquent\Model
{
    protected $tablePrefix = 'Animal';
    protected $primaryKey = null;

    /**
     * A callable to translate the classname to a database table name
     *
     * @param string $classname
     * @return string
     */
    protected function tableCallable($classname) {
        // Default behaviour
        // return Str::snake(Str::pluralStudly($classname));
        if ($classname == $this->tablePrefix) return 'tbl'.$this->tablePrefix;
        return 'tbl'.$this->tablePrefix.self::nonPluralStudly($classname);
    }

    /**
     * A callable to determine the primary key for the model
     *
     * @param string $tablename
     * @param string $classname
     * @return string
     */
    protected function primaryKeyCallable($tablename, $classname) {
        return self::nonPluralStudly($classname).'Id';
        // Default behaviour
        // return $this->getTable().'_id';
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? $this->tableCallable(class_basename($this));
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey ?? $this->primaryKeyCallable($this->getTable(), class_basename($this));
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
//        $this->bootIfNotBooted();
//        $this->initializeTraits();
//        $this->syncOriginal();
//        $this->fill($attributes);
    }

    /**
     * Get the plural form of an English word.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    public static function plural($value, $count = 2)
    {
        return Pluralizer::plural($value, $count);
    }
    /**
     * StudlyCapsCase string.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    public static function nonPluralStudly($value, $count = 2)
    {
        return Str::studly($value);
    }
}

