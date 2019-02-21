?php
/**
 * Define a custom exception class
 */
class MyException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction() {
        echo "A custom function for this type of exception\n";
    }
}


class Test
{
    public function go()
    {
        if(0==0)
            throw new MyException("Error Processing Request", 1);

    }
}

class Runner
{
    public function run()
    {
        try
        {
            $test = new  Test();
            $test->go();
        }catch(MyException $mye){
            var_dump($mye);
        }catch(Exception $e){
            var_dump($e);
        }
    }
}

$runner = new Runner();
$runner->run();