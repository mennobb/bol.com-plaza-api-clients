using System;
using System.IO;
using System.Windows.Forms;
using System.Xml;
using System.Xml.Serialization;

namespace bol.com.PlazaAPI.Test
{
    /// <summary>
    /// This class is used only for testing purposes.
    /// </summary>
    public partial class FormTest : Form
    {
        #region Constructors

        /// <summary>
        /// Initializes a new instance of the <see cref="FormTest"/> class.
        /// </summary>
        public FormTest()
        {
            InitializeComponent();
        }

        #endregion

        #region Methods

        /// <summary>
        /// This code demonstrates how to get all currently open orders.
        /// This code performs a simple GET on the "/services/rest/orders/v1/open/" PlazaAPI.
        /// </summary>
        /// <param name="plazaAPIClient">The plaza API client.</param>
        private static void GetOrders(PlazaAPIClient plazaAPIClient)
        {
            OpenOrders openOrders = plazaAPIClient.GetOrders();
        }

        /// <summary>
        /// This code demonstrates how to send 1 or more shipping/cancellation notifications to bol.com
        /// This code performs a simple POST on the "/services/rest/orders/v1/process/" PlazaAPI.
        /// See the example code below for typical usage instructions.
        /// </summary>
        /// <param name="plazaAPIClient">The plaza API client.</param>
        private static void ProcessOrders(PlazaAPIClient plazaAPIClient)
        {
            // Create a new batch. 
            // This is required before calling the method "PlazaAPIClient.ProcessOrders()"
            
            /*
            ProcessOrders processOrders = new ProcessOrders()
            {
                // Add a shipment
                Shipments = new ProcessOrdersShipment[1] { GenerateShipment() },

                // Add a cancellation
                Cancellations = new ProcessOrdersCancellation[1] { GenerateCancellation() }
            };
            */
            
            ProcessOrders processOrders = new ProcessOrders();

            // Create 5 random shipments
            processOrders.Shipments = new ProcessOrdersShipment[5];

            for (int i = 0; i < processOrders.Shipments.Length; i++)
            {
                processOrders.Shipments[i] = GenerateShipment();
            }

            // Create 5 random cancellations
            processOrders.Cancellations = new ProcessOrdersCancellation[5];

            for (int i = 0; i < processOrders.Cancellations.Length; i++)
            {
                processOrders.Cancellations[i] = GenerateCancellation();
            }

            // Done! Let's push this batch to the server.
            ProcessOrdersResult processOrdersResult = plazaAPIClient.ProcessOrders(processOrders);
        }
        
        /// <summary>
        /// Generates the cancellation.
        /// </summary>
        /// <returns>A new ProcessOrdersCancellation.</returns>
        private static ProcessOrdersCancellation GenerateCancellation()
        {
            ProcessOrdersCancellation cancellation = new ProcessOrdersCancellation();
            cancellation.OrderId = 98765;
            cancellation.DateTime = new DateTime(2014, 4, 7, 17, 58, 13);
            cancellation.ReasonCode = ProcessOrdersCancellationReasonCode.OUT_OF_STOCK;
            cancellation.OrderItems = new long[] { 12, 34, 56 };

            return cancellation;
        }

        /// <summary>
        /// Generates the shipment.
        /// </summary>
        /// <returns>A new ProcessOrdersShipment.</returns>
        private static ProcessOrdersShipment GenerateShipment()
        {
            ProcessOrdersShipment shipment = new ProcessOrdersShipment();
            shipment.OrderId = 12345;
            shipment.DateExpectedDelivery = new DateTime(2015, 02, 07);
            shipment.DateTime = new DateTime(2014, 4, 7, 17, 58, 13);
            ProcessOrdersShipmentTransporter transporter = new ProcessOrdersShipmentTransporter();
            transporter.Code = "TNT";
            transporter.TrackAndTraceCode = "12345";
            shipment.Transporter = transporter;
            shipment.OrderItems = new long[] { 998877, 778899 };

            return shipment;
        }

        /// <summary>
        /// This code demonstrates how to send 1 or more shipping/cancellation notifications to bol.com from a xml file.
        /// This code performs a simple POST on the "/services/rest/orders/v1/process/" PlazaAPI.
        /// See the example code below for typical usage instructions.
        /// </summary>
        /// <param name="plazaAPIClient">The plaza API client.</param>
        private static void ProcessOrdersFromXml(PlazaAPIClient plazaAPIClient)
        {
            // Create an instance of the XmlSerializer specifying type and namespace
            XmlSerializer serializer = new XmlSerializer(typeof(ProcessOrders));

            // A FileStream is needed to read the XML document.
            FileStream fs = new FileStream(@"C:\WorkingCopy\Projects\bol.com\bol.com-plaza-api-clients-master\c#\bol.com.PlazaAPI\bol.com.PlazaAPI.Test\ProcessOrderTest.xml", FileMode.Open);
            XmlReader reader = XmlReader.Create(fs);

            // Declare an object variable of the type to be deserialized.
            ProcessOrders processOrders;

            // Use the Deserialize method to restore the object's state.
            processOrders = (ProcessOrders)serializer.Deserialize(reader);
            fs.Close();

            // Done! Let's push this batch to the server.
            ProcessOrdersResult processOrdersResult = plazaAPIClient.ProcessOrders(processOrders);
        }

        /// <summary>
        /// This code demonstrates how to get the process status.
        /// This code performs a simple GET on the "/services/rest/orders/v1/process/" PlazaAPI.
        /// </summary>
        /// <param name="plazaAPIClient">The plaza API client.</param>
        private static void GetProcessStatus(PlazaAPIClient plazaAPIClient)
        {
            ProcessOrdersOverview processOrdersOverview = plazaAPIClient.GetProcessStatus(123);
        }

        /// <summary>
        /// This code demonstrates how to get all payments for a specific month.
        /// This code performs a simple GET on the "/services/rest/payments/v1/payments/" PlazaAPI.
        /// </summary>
        /// <param name="plazaAPIClient">The plaza API client.</param>
        private static void GetPaymentsForMonth(PlazaAPIClient plazaAPIClient)
        {
            Payments payments = plazaAPIClient.GetPaymentsForMonth(2014, 2);
        }

        /// <summary>
        /// Handles the Click event of the CleanButton control.
        /// </summary>
        /// <param name="sender">The source of the event.</param>
        /// <param name="e">The <see cref="EventArgs"/> instance containing the event data.</param>
        private void CleanButton_Click(object sender, EventArgs e)
        {
            txtRequest.Text = string.Empty;
            txtResponse.Text = string.Empty;
        }

        /// <summary>
        /// Handles the Click event of the LaunchButton control.
        /// </summary>
        /// <param name="sender">The source of the event.</param>
        /// <param name="e">The <see cref="EventArgs"/> instance containing the event data.</param>
        private void LaunchButton_Click(object sender, EventArgs e)
        {
            try
            {
                EnableForm(false);

                // Instantiate the PlazaAPIClient class by passing it the public and private key and the URL, i.e.: "https://test-plazaapi.bol.com"
                PlazaAPIClient plazaAPIClient = new PlazaAPIClient(txtAccessKeyId.Text, txtSecretAccessKey.Text, txtUrl.Text);

                if (optOpenOrders.Checked)
                {
                    GetOrders(plazaAPIClient);
                }
                
                if (optShipmentsAndCancellations.Checked)
                {
                    ProcessOrders(plazaAPIClient);
                }

                if (optShipmentsAndCancellationsXml.Checked)
                {
                    ProcessOrdersFromXml(plazaAPIClient);
                }

                if (optProcessOrdersOverview.Checked)
                {
                    GetProcessStatus(plazaAPIClient);
                }

                if (optPayments.Checked)
                {
                    GetPaymentsForMonth(plazaAPIClient);
                }

                txtRequest.Text = plazaAPIClient.SigningRequest;
                txtResponse.Text = plazaAPIClient.ResponseOutput;
            }
            catch (PlazaAPIException customEx)
            {
                MessageBox.Show("Error Code: " + customEx.ErrorCode.ToString() + Environment.NewLine +
                                "Error Message: " + customEx.Message + Environment.NewLine +
                                "Trace ID: " + customEx.TraceId + Environment.NewLine);
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
            finally
            {
                EnableForm(true);
            }
        }

        /// <summary>
        /// Enables all the form's controls.
        /// </summary>
        /// <param name="enabled">if set to <c>true</c> [enabled].</param>
        private void EnableForm(bool enabled)
        {
            txtAccessKeyId.Enabled = enabled;
            txtSecretAccessKey.Enabled = enabled;
            txtUrl.Enabled = enabled;

            optOpenOrders.Enabled = enabled;
            optShipmentsAndCancellations.Enabled = enabled;
            optShipmentsAndCancellationsXml.Enabled = enabled;
            optProcessOrdersOverview.Enabled = enabled;
            optPayments.Enabled = enabled;

            txtRequest.Enabled = enabled;
            txtResponse.Enabled = enabled;

            LaunchButton.Enabled = enabled;
            CleanButton.Enabled = enabled;
        }

        #endregion
    }
}
