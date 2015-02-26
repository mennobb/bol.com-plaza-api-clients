namespace bol.com.PlazaAPI
{
    /// <summary>
    /// All the constants used within this project.
    /// </summary>
    public class C
    {
        /// <summary>
        /// The request methods.
        /// </summary>
        public class RequestMethods
        {
            /// <summary>
            /// The GET method.
            /// </summary>
            public const string GET = "GET";

            /// <summary>
            /// The POST method.
            /// </summary>
            public const string POST = "POST";
            
            /// <summary>
            /// The PUT method.
            /// </summary>
            public const string PUT = "PUT";
            
            /// <summary>
            /// The DELETE method.
            /// </summary>
            public const string DELETE = "DELETE";
        }

        /// <summary>
        /// The Plaza API request constants.
        /// </summary>
        public class PlazaAPIRequest
        {
            /// <summary>
            /// Some custom headers for the Plaza API request.
            /// </summary>
            public class Headers
            {
                /// <summary>
                /// The X-BOL-Date request header.
                /// </summary>
                public const string Date = "X-BOL-Date";
                
                /// <summary>
                /// The X-BOL-Date format.
                /// </summary>
                public const string DateFormat = "ddd, dd MMM yyyy HH':'mm':'ss 'GMT'";
                
                /// <summary>
                /// The X-BOL-Authorization request header.
                /// </summary>
                public const string Authorization = "X-BOL-Authorization";
            }
        }
    }
}
