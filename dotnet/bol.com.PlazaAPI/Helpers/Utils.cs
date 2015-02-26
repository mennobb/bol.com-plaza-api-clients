using System;
using System.Globalization;
using System.Net;
using System.Security.Cryptography;
using System.Text;

namespace bol.com.PlazaAPI.Helpers
{
    /// <summary>
    /// This class provides helper methods to perform a request with the Plaza API.
    /// </summary>
    public static class Utils
    {
        #region Properties

        /// <summary>
        /// Gets or sets the string to sign.
        /// </summary>
        /// <value>
        /// The string to sign.
        /// </value>
        public static string StringToSign { get; set; }

        #endregion

        #region Methods

        /// <summary>
        /// Handles the request.
        /// </summary>
        /// <param name="request">The request.</param>
        /// <param name="method">The method.</param>
        /// <param name="accessKeyId">The access key identifier.</param>
        /// <param name="secretAccessKey">The secret access key.</param>
        public static void HandleRequest(HttpWebRequest request, string method, string accessKeyId, string secretAccessKey)
        {
            // Method
            request.Method = method;

            // Content-Type
            request.ContentType = "application/xml";
    
            // Date
            request.Date = DateTime.UtcNow;
            request.Headers[C.PlazaAPIRequest.Headers.Date] = DateTime.UtcNow.ToString(C.PlazaAPIRequest.Headers.DateFormat, CultureInfo.InvariantCulture);

            // Authorization
            StringToSign = Utils.CreateStringToSign(request);
            request.Headers[C.PlazaAPIRequest.Headers.Authorization] = accessKeyId + ":" + Utils.CalculateHMAC256(StringToSign, secretAccessKey);
        }

        /// <summary>
        /// Calculates the HMAC256 string based on the given string and secret access key.
        /// </summary>
        /// <param name="stringToSign">The string to sign.</param>
        /// <param name="secretAccessKey">The secret access key to sign the string with.</param>
        /// <returns>The calculated HMAC256 string.</returns>
        private static string CalculateHMAC256(string stringToSign, string secretAccessKey)
        {
            UTF8Encoding encoding = new UTF8Encoding();

            HMACSHA256 hmac = new HMACSHA256(encoding.GetBytes(secretAccessKey));
            byte[] hash = hmac.ComputeHash(encoding.GetBytes(stringToSign));

            return Convert.ToBase64String(hash);
        }

        /// <summary>
        /// Creates the string to sign.
        /// </summary>
        /// <param name="request">The request.</param>
        /// <returns>The string to sign.</returns>
        private static string CreateStringToSign(HttpWebRequest request)
        {
            StringBuilder sb = new StringBuilder(256);

            sb.Append(request.Method);
            sb.Append("\n\n");

            sb.Append(request.ContentType);
            sb.Append("\n");

            sb.Append(request.Headers[C.PlazaAPIRequest.Headers.Date]);
            sb.Append("\n");

            sb.Append(C.PlazaAPIRequest.Headers.Date.ToLower() + ":" + request.Headers[C.PlazaAPIRequest.Headers.Date]);
            sb.Append("\n");

            sb.Append(request.RequestUri.AbsolutePath);

            return sb.ToString();
        }

        #endregion
    }
}