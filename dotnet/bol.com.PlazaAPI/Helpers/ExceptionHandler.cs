using System;
using System.Net;
using System.Xml.Serialization;

namespace bol.com.PlazaAPI.Helpers
{
    /// <summary>
    /// This class handles the service error of the PlazaAPI.
    /// </summary>
    public class ExceptionHandler
    {
        #region Methods

        /// <summary>
        /// Handles the response exception.
        /// </summary>
        /// <param name="response">The response.</param>
        /// <returns>A custom exception.</returns>
        public static PlazaAPIException HandleResponseException(HttpWebResponse response)
        {
            try
            {
                XmlRootAttribute xmlRoot = new XmlRootAttribute();
                xmlRoot.ElementName = "serviceError";
                xmlRoot.Namespace = "http://config.services.com/schemas/bol-messages-1.0.xsd";
                xmlRoot.IsNullable = true;

                XmlSerializer ser = new XmlSerializer(typeof(ServiceError), xmlRoot);
                object obj = ser.Deserialize(response.GetResponseStream());
                ServiceError error = (ServiceError)obj;

                return new PlazaAPIException(error.ErrorMessage, error.ErrorCode, error.TraceId);
            }
            catch (Exception)
            {
                throw;
            }
        }

        #endregion
    }
}
