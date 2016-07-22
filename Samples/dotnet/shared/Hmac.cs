using System;
using System.Collections.Generic;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Web;

namespace PayJS_Samples.Misc
{
    public static class Hmac
    {
        public static string GetHmac(string HashString, string PrivateKey)
        {
            byte[] HashStringBytes = UTF8Encoding.UTF8.GetBytes(HashString);
            byte[] MerchantKeyBytes = UTF8Encoding.UTF8.GetBytes(PrivateKey);
            
            HMACSHA512 HmacObj = new HMACSHA512(MerchantKeyBytes);
            byte[] ResultBytes = HmacObj.ComputeHash(HashStringBytes);
            
            string ResultString = Convert.ToBase64String(ResultBytes);
            return ResultString;
        }
    }
}