<?php

namespace App\Models\test;

use App\Models\SchFeeStructureVoteHead;
use App\Models\SchStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHolder extends Model
{
    use HasFactory;
     /**
     * Get the names of all defined relationships for the model.
     *
     * @return array
     */
    public static function getRelationshipNames($tableName,$modelInstance)
    {
        try {
            // Use reflection to inspect all methods of the model
            $class = new \ReflectionClass($modelInstance);
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
    
            $relations = [];
            $common = ['get_fillable', 'factory'];
    
            foreach ($methods as $method) {
                $className = $method->class;
                if (strpos($className, 'App\\Models\\') === 0) {
                    $relations[] = $method->name;
                }
            }
           
    
            return array_diff($relations, $common);
        } catch (\Throwable $e) {
            // Handle exceptions and return false
            \Log::error("Error in getRelationshipNames for table '$tableName': " . $e->getMessage());
            return [];
        }
    }
    
}
