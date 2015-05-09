using System;
using System.Windows.Forms;
using System.Net;
using System.Web.Script.Serialization;
using System.Collections.Generic;
using System.Collections;
using System.IO;

/**
 * ADDITIONAL FEATURES:
 *  Circle/Golden Rectangle Generator
 *  Food guide
 *  Brewing guide
 *  Customizable Redstone Circuitry Dictionary
 *  Coordinate Tracker
**/ 


namespace MinecraftUtil
{
    public partial class Form1 : Form
    {
        private const string _QUERY = "http://api.minetools.eu/query/{0}/{1}";
        private const string _PING = "http://api.minetools.eu/ping/{0}/{1}";

        public Form1()
        {
            InitializeComponent();

            if (!File.Exists( "MinecraftUtil.exe.config" ))
            {
                CreateConfigFile();
            }

            // write settings data
            textBox1.Text = Properties.Settings.Default.ServerAddress;
            numericUpDown1.Value = Properties.Settings.Default.ServerPort;

            // start timer for fetching data
            timer1.Start();
            RefreshData();
        }

        private void RefreshData()
        {
            try
            {
                // get the query strings
                string queryString  = new WebClient().DownloadString( string.Format( _QUERY, textBox1.Text, numericUpDown1.Value.ToString() ) );
                //string pingString   = new WebClient().DownloadString( string.Format( _PING,  textBox1.Text, numericUpDown1.Value.ToString() ) );

                // de-serialize into a dictionary
                var ser = new JavaScriptSerializer();
                Dictionary<string,object> dict = ser.Deserialize<Dictionary<string, object>>( queryString );

                // populate data fields from dict
                groupPlayers.Text = "Players Online: " + dict["Players"].ToString();
                labelMotd.Text = dict["HostName"].ToString();
                ListPlayers( dict["Playerlist"] );

                // finally, if nothing has gone wrong, show 'connected' status
                toolStripStatusLabel2.Text = "Connected";
            }
            catch (WebException e)          { ResetData(); }
            catch (KeyNotFoundException e)  {  }
            catch (ArgumentException e)     { ResetData(); }
        }

        private void ResetData()
        {
            toolStripStatusLabel2.Text = "Not connected";
            labelPlayers.Text = "";
            labelMotd.Text = "";
            groupPlayers.Text = "Players Online: ";
        }

        private void ListPlayers( object list )
        {
            // build the list of online players
            if ( list.ToString() == "null" )
            {
                labelPlayers.Text = "No players are online";
            }
            else
            {
                ArrayList array = (ArrayList)list;

                // play with the label text
                labelPlayers.Text = "";
                foreach (string s in array)
                {
                    // append online players
                    labelPlayers.Text += s + "\r\n";
                }
            }
        }

        void Timer1Tick(object sender, EventArgs e)
        {
            // every 5000ms refresh the data
            RefreshData();
        }

        void Form1FormClosing(object sender, FormClosingEventArgs e)
        {
            // save ip and port data
            Properties.Settings.Default.ServerAddress = textBox1.Text;
            Properties.Settings.Default.ServerPort = numericUpDown1.Value;
            Properties.Settings.Default.Save();
        }

        private void toolStripStatusLabel2_Click(object sender, EventArgs e)
        {
            RefreshData();
        }

        private void CreateConfigFile()
        {
            // this is disgusting and i'm ashamed of myself, go away
            File.WriteAllText( "MinecraftUtil.exe.config", "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n<configuration>\r\n\t<configSections>\r\n\t\t<sectionGroup name=\"userSettings\" type=\"System.Configuration.UserSettingsGroup, System, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089\" >\r\n\t\t\t<section name=\"MinecraftUtil.Properties.Settings\" type=\"System.Configuration.ClientSettingsSection, System, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089\" allowExeDefinition=\"MachineToLocalUser\" requirePermission=\"false\" />\r\n\t\t</sectionGroup>\r\n\t</configSections>\r\n\t<userSettings>\r\n\t\t<MinecraftUtil.Properties.Settings>\r\n\t\t\t<setting name=\"ServerAddress\" serializeAs=\"String\">\r\n\t\t\t\t<value />\r\n\t\t\t</setting>\r\n\t\t\t<setting name=\"ServerPort\" serializeAs=\"String\">\r\n\t\t\t\t<value>0</value>\r\n\t\t\t</setting>\r\n\t\t</MinecraftUtil.Properties.Settings>\r\n\t</userSettings>\r\n</configuration>" );
        }
    }
}
