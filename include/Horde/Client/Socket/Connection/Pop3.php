<?php
/**
 * Copyright 2013 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * PHP stream connection to the POP3 server.
 *
 * NOTE: This class is NOT intended to be accessed outside of the package.
 * There is NO guarantees that the API of this class will not change across
 * versions.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013 Horde LLC
 * @internal
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Socket_Connection_Pop3
extends Horde_Imap_Client_Socket_Connection
{
    /**
     * Writes data to the POP3 output stream.
     *
     * @param string $data  String data.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function write($data)
    {
        if (fwrite($this->_stream, $data . "\r\n") === false) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::t("Server write error."),
                Horde_Imap_Client_Exception::SERVER_WRITEERROR
            );
        }

        $this->_debug->client($data);
    }

    /**
     * Read data from incoming POP3 stream.
     *
     * @return string  Line of data.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function read()
    {
        if (feof($this->_stream)) {
            $this->close();
            $this->_debug->info("ERROR: Server closed the connection.");
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::t("POP3 Server closed the connection unexpectedly."),
                Horde_Imap_Client_Exception::DISCONNECT
            );
        }

        if (($read = fgets($this->_stream)) === false) {
            $this->_debug->info("ERROR: IMAP read/timeout error.");
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::t("Error when communicating with the mail server."),
                Horde_Imap_Client_Exception::SERVER_READERROR
            );
        }

        $this->_debug->server(rtrim($read, "\r\n"));

        return $read;
    }

}
