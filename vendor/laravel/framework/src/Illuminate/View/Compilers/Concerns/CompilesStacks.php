<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesStacks
{
    /**
     * Compile the stack statements into the content.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileStack($expression)
    {
        return "<?php echo \$__env->yieldPushContent{$expression}; ?>";
    }

    /**
     * Compile the push statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compilePush($expression)
    {
        return "<?php \$__env->startPush{$expression}; ?>";
    }

    /**
     * Compile the end-push statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndpush()
    {
        return '<?php $__env->stopPush(); ?>';
    }

    /**
     * Compile the prepend statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compilePrepend($expression)
    {
        return "<?php \$__env->startPrepend{$expression}; ?>";
    }

    /**
     * Compile the end-prepend statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndprepend()
    {
        return '<?php $__env->stopPrepend(); ?>';
    }
}
