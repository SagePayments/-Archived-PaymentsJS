using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Web;

namespace PayJS_Samples.Misc
{
    public static class Shared
    {
        public static string DeveloperID = "GTq2h4mXxLIBtzbOWLO2GwqZfOgK8BbT";
        public static string DeveloperKEY = "ICkrA2n6HIleJ663";
        public static string Environment = "qa";
        public static string MerchantID = "999999999997";
        public static string MerchantKEY = "K3QD6YWyhfD";

        public static string GetAuthKey(string toHash, string privateKey, Tuple<byte[], string> nonce)
        {
            toHash = UTF8Encoding.UTF8.GetString(UTF8Encoding.UTF8.GetBytes(toHash));
            string passphrase = privateKey;
            string salt = nonce.Item2;
            byte[] iv = nonce.Item1;

            byte[] encryptedResult;
            using (Aes aesAlg = Aes.Create())
            {
                using (Rfc2898DeriveBytes pbkdf2 = new Rfc2898DeriveBytes(passphrase, UTF8Encoding.UTF8.GetBytes(salt), 1500))
                {
                    aesAlg.Key = pbkdf2.GetBytes(32);
                }
                aesAlg.IV = iv;
                aesAlg.Padding = PaddingMode.PKCS7;

                ICryptoTransform encryptor = aesAlg.CreateEncryptor(aesAlg.Key, aesAlg.IV);

                using (MemoryStream msEncrypt = new MemoryStream())
                {
                    using (CryptoStream csEncrypt = new CryptoStream(msEncrypt, encryptor, CryptoStreamMode.Write))
                    {
                        using (StreamWriter swEncrypt = new StreamWriter(csEncrypt))
                        {
                            swEncrypt.Write(toHash);
                        }
                        encryptedResult = msEncrypt.ToArray();
                    }
                }
            }
            ;
            return Convert.ToBase64String(encryptedResult);
        }

        public static Tuple<byte[], string> GetNonce()
        {
            string nonce = Guid.NewGuid().ToString().Replace("-", "").Substring(0, 16);
            byte[] nonceBytes = UTF8Encoding.UTF8.GetBytes(nonce);
            return new Tuple<byte[], string>(nonceBytes, Convert.ToBase64String(UTF8Encoding.UTF8.GetBytes(BytesToHex(nonceBytes))));
        }

        private static string BytesToHex(byte[] ba)
        {
            StringBuilder hex = new StringBuilder(ba.Length * 2);
            foreach (byte b in ba)
                hex.AppendFormat("{0:x2}", b);
            return hex.ToString();
        }
    }
}