using System;
using System.Runtime.Serialization;
using System.Security.Permissions;

namespace bol.com.PlazaAPI
{
    /// <summary>
    /// This class provides a custom exception.
    /// </summary>
    [Serializable]
    public class PlazaAPIException : Exception
    {
        #region Constructors

        /// <summary>
        /// Initializes a new instance of the <see cref="PlazaAPIException"/> class.
        /// </summary>
        public PlazaAPIException() : base()
        {
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="PlazaAPIException"/> class.
        /// </summary>
        /// <param name="message">The message.</param>
        /// <param name="errorCode">The error code.</param>
        /// <param name="traceId">The trace identifier.</param>
        public PlazaAPIException(string message, int errorCode, string traceId) : base(message)
        {
            ErrorCode = errorCode;
            TraceId = traceId;
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="PlazaAPIException"/> class.
        /// </summary>
        /// <param name="info">The <see cref="T:System.Runtime.Serialization.SerializationInfo" /> that holds the serialized object data about the exception being thrown.</param>
        /// <param name="context">The <see cref="T:System.Runtime.Serialization.StreamingContext" /> that contains contextual information about the source or destination.</param>
        protected PlazaAPIException(SerializationInfo info, StreamingContext context) : base(info, context)
        { 
        }

        #endregion

        #region Properties

        /// <summary>
        /// Gets or sets the error code.
        /// </summary>
        /// <value>
        /// The error code.
        /// </value>
        public int ErrorCode { get; set; }

        /// <summary>
        /// Gets or sets the trace identifier.
        /// </summary>
        /// <value>
        /// The trace identifier.
        /// </value>
        public string TraceId { get; set; }

        #endregion
    }
}
