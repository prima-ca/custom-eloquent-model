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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use LogicException;

abstract class CustomModel extends \Illuminate\Database\Eloquent\Model
{
//    use
//        Concerns\HasAttributes,
//        Concerns\HasEvents,
//        Concerns\HasGlobalScopes,
//          Concerns\CustomHasRelationships
//        Concerns\HasTimestamps,
//        Concerns\HidesAttributes,
//        Concerns\GuardsAttributes,
//        ForwardsCalls;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $tablePrefix = 'Animal';
    protected $primaryKey = null;
    protected $foreignKey = null;


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

    protected function foreignKeyCallable($tablename, $classname) {
        return self::nonPluralStudly($classname).'Id';
        // Default
        // return Str::snake(class_basename($this)).'_'.$this->getKeyName();
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
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey ?? $this->foreignKeyCallable($this->getTable(), class_basename($this));
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
        $this->bootIfNotBooted();
        $this->initializeTraits();
        $this->syncOriginal();
        $this->fill($attributes);
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

    protected static function booted() {
        static::creating(function($model) {
            $id = Auth::id();
            $model->created_by = $id;
        });
        static::updating(function($model) {
            $id = Auth::id();
            $model->updated_by = $id;
        });
        static::deleting(function($model) {
            $id = Auth::id();
            $model->deleted_by = $id;
        });
    }
}

