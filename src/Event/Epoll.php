<?php
/**
 * Created by PhpStorm.
 * User: sxg
 * Date: 2022/9/2
 * Time: 16:14
 */

namespace Socket\Ms\Event;

class Epoll implements Event
{
    public $_eventBase;
    public $_allEvents;
    public $_signalEvents;

    public static $_timerId = 1;
    public $_timers = [];

    public function __construct() {
        $this->_eventBase = new \EventBase();
    }

    public function timerCallBack($fd, $what, $arg) {
        $func = $arg[0];
        $flag = $arg[1];
        $timerId = $arg[2];
        $userArg = $arg[3];

        echo "执行timerCallBack\r\n";
        if ($flag == Event::EV_TIMER_ONCE){
            $event = $this->_timers[$timerId][$flag];
            $event->del();
            unset($this->_timers[$timerId][$flag]);
        }
        call_user_func_array($func,$userArg);
    }

    public function add($fd, $flag, $func, $args = [])
    {
        // TODO: Implement add() method.
        switch ($flag) {
            case self::READ:
                $event = new \Event($this->_eventBase, $fd, \Event::READ|\Event::PERSIST, $func, $args);
                if (!$event || !$event->add()) {
                    fprintf(STDOUT, "事件添加失败\n");
                    print_r(error_get_last());
                    return false;
                }
                $this->_allEvents[(int)$fd][Event::READ] = $event;
                break;
            case self::WRITE:
                $event = new \Event($this->_eventBase, $fd, \Event::WRITE|\Event::PERSIST, $func, $args);
                if (!$event || !$event->add()) {
                    fprintf(STDOUT, "事件添加失败\n");
                    print_r(error_get_last());
                    return false;
                }
                $this->_allEvents[(int)$fd][Event::WRITE] = $event;
                break;
            case self::EVENT_TIMER:
            case self::EV_TIMER_ONCE:
                $timer_id = self::$_timerId;
                $params = [$func, $flag, $timer_id, $args];
                $event = new \Event($this->_eventBase, -1, self::EVENT_TIMER|\Event::PERSIST, [$this, "timerCallBack"], $params);
                if (!$event || !$event->add($fd)) {
                    echo "定时事件添加失败\r\n";
                    return false;
                }
                echo "定时事件添加成功\r\n";
                $this->_timers[$timer_id][$flag] = $event;
                ++self::$_timerId;
                break;
        }
    }

    public function del($fd, $flag)
    {
        // TODO: Implement del() method.
        switch ($flag) {
            case self::READ:
                if (isset($this->_allEvents[(int)$fd][self::READ])) {
                    $event = $this->_allEvents[(int)$fd][self::READ];
                    $event->del();
                    unset($this->_allEvents[(int)$fd][self::READ]);
                }
                if (empty($this->_allEvents[(int)$fd])) {
                    unset($this->_allEvents[(int)$fd]);
                }
                break;
            case self::WRITE:
                if (isset($this->_allEvents[(int)$fd][self::WRITE])) {
                    $event = $this->_allEvents[(int)$fd][self::WRITE];
                    $event->del();
                    unset($this->_allEvents[(int)$fd][self::WRITE]);
                }
                if (empty($this->_allEvents[(int)$fd])) {
                    unset($this->_allEvents[(int)$fd]);
                }
                break;
        }

    }

    public function loop()
    {
        // TODO: Implement loop() method.
        echo "执行事件循环了\r\n";
        return $this->_eventBase->loop();
    }

    public function clearTimer()
    {
        // TODO: Implement clearTimer() method.
    }

    public function clearSignalEvents()
    {
        // TODO: Implement clearSignalEvents() method.
    }
}