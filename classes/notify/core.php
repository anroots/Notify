<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Stores messages of different types, and being a singleton it can be called
 * in any place of the code, collect all the messages and later render them
 * with a unified look.
 *
 * 
 * @package    Notify
 * @author     Ricardo de Luna <kaltar@kaltar.org>
 * @copyright  (c) 2010 Ricardo de Luna
 *
 */
class Notify_Core
{
	/**
	 * Singleton static instance
	 * 
 	 * (default value: NULL)
 	 *
	 * @var mixed
	 * @access private
	 */
	private static $instance				= NULL;

	/**
	 * 2-D Array storing message type($key) and messages ($value = array)
	 * 
	 * @var array
	 * @access private
	 */
	private static $msgs					= array(); 

	/**
	 * Stores the view to render the notices
	 * 
	 * (default value: NULL)
	 * 
	 * @var string
	 * @access private
	 */
	private static $view					= NULL;

	/**
	 * Stores the default message type
	 * 
	 * (default value: NULL)
	 * 
	 * @var string
	 * @access private
	 */
	private static $default_message_type	= NULL;
	
	/**
	 * Stores the message in an array
	 * 
	 * @access public
	 * @static
	 * @param string $msg
	 * @param string $type. (default: 'information')
	 * @return chainable
	 */
	public static function msg($msg, $type = NULL)
	{
		// If we receive a message with no type
		if (is_null($type))
		{
			// If we haven't assigned a default message type
			if (is_null(self::$default_message_type))
			{
				// Get value from config file
				self::$default_message_type = trim(Kohana::config('notify.default_message_type'));
			}
			// Assign value
			$type = self::$default_message_type;
		}
		else
		{
			$type = trim($type);
		}
		// Force casting and sanitizing
		$msg = trim($msg);

		// See if we do not already have a key for that type of message
		// initialize the array
		if ( ! array_key_exists($type, self::$msgs))
		{
			self::$msgs[$type] = array();
		}
		
		self::$msgs[$type][] = $msg;
		
		// Make it chainable
		return self::return_instance();
	}

	/**
	 * Sets the default message type
	 * 
	 * @access public
	 * @static
	 * @param string $view. (example: 'error')
	 * @return chainable
	 */
	public static function default_message_type($type)
	{
		self::$default_message_type = trim($type);
		return self::return_instance();
	}

	/**
	 * Restores the default message type to the configuration file
	 * 
	 * @access public
	 * @static
	 * @return chainable
	 */
	public static function restore_default_message_type()
	{
		// Get value from config file
		self::$default_message_type = trim(Kohana::config('notify.default_message_type'));
		return self::return_instance();
	}
	
	/**
	 * Sets the view to use while rendering
	 * 
	 * @access public
	 * @static
	 * @param string $view. (example: 'notify/notify')
	 * @return chainable
	 */
	public static function view($view)
	{
		self::$view = trim($view);
		return self::return_instance();
	}

	/**
	 * Renders the messages in the view
	 * if $message_type is specified, will only render 
	 * the messages of the type $message_type
	 * 
	 * @access public
	 * @static
	 * @param mixed $message_type. (default: NULL)
	 * @return string
	 */
	public static function render($message_type = NULL)
	{
		// If view is not assigned, get from config file
		if (is_null(self::$view))
		{
			self::$view = Kohana::config('notify.view');
		}
		
		// If it's valid $message_type received, we should only render the messages of that type
		if ( ! is_null($message_type) AND array_key_exists($message_type, self::$msgs))
		{
			$vars = array('msgs' => array($message_type => self::$msgs[$message_type]));
		}
		else
		{
			// Render all messages
			$vars = array('msgs' => self::$msgs);
		}

		// Render the view		
		$messages =  View::factory(self::$view, $vars)->render();

		// Return the rendered messages
		return $messages;
	}

	/**
	 * Get the singleton instance of Kohana_Notify.
	 *
	 * @return  Kohana_Notify
	 */
	private static function return_instance()
	{
		if (self::$instance === NULL)
		{
			// Assign self
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	final private function __construct()
	{
		// Enforce singleton behavior
	}

	final private function __clone()
	{
		// Enforce singleton behavior
	}

}

