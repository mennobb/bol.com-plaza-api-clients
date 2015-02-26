namespace bol.com.PlazaAPI.Helpers
{
    /// <summary>
    /// This class represents the service error that the PlazaAPI returns when a exception occurs.
    /// </summary>
    public class ServiceError
    {
        /// <summary>
        /// Gets or sets the error code, e.g. 41000
        /// </summary>
        public int ErrorCode { get; set; }

        /// <summary>
        /// Gets or sets the error code and error message. Format: BOL-ERROR [PAI-41000] = The message text
        /// </summary>
        /// <value>
        /// The error message.
        /// </value>
        public string ErrorMessage { get; set; }

        /// <summary>
        /// Gets or sets the trace identifier.
        /// </summary>
        /// <value>
        /// The trace identifier.
        /// </value>
        public string TraceId { get; set; }
    }
}
