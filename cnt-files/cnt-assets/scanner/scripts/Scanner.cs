#r "bin\kodakscansdk.dll"

using System;
using System.Runtime.InteropServices;
using System.Diagnostics;
using System.Threading.Tasks;
using KODAKSCANSDK;

public partial class Startup
{
    private static readonly string TAG = "[Scanner] "; // TAG for debugging
    private IntPtr hWnd; // Window handler
    private int retVal; // Return value
    private static bool mSubscribed;
    private static readonly string filePrefix = "canhoto-nota-"; // The prefix of the file name
    private static readonly string filePath = "Images"; // The path where the images will be saved
    private string textBoxMessage = "";
    private ScanEventHandler scanEventHandler; // The scan event
    private int scannedQuantity = 0;
    private string scanDir = "";

    // Import user32.dll for the GetForegroundWindow method (Retrieves a handle to the foreground window - HWND)
    [DllImport("user32.dll")]
    static extern IntPtr GetForegroundWindow();

    // Get the Kodak Scan SDK program
    static KODAKSCANSDK.Program myKodakscansdk;

    public async Task<object> Invoke(dynamic input)
    {
        hWnd = GetForegroundWindow();
        scannedQuantity = 0;
        if (myKodakscansdk == null) myKodakscansdk = new KODAKSCANSDK.Program();
        //Console.WriteLine(TAG + "hWnd: " + hWnd);
        // If the scan event already exists, just start scanning
        if(scanEventHandler == null) {
            scanEventHandler = new ScanEventHandler(delegate (string args)
            {
                // The event to run each time something is scanned (or not)
                //Console.WriteLine(TAG + "MyScanEvent " + args);
                // 0 - scan completed, no more images left, 1 - a page was scanned
                if (args != "1")
                {
                    CloseScanner();
                    input.onFinish(scannedQuantity);
                } else scannedQuantity++;
            });
            KODAKSCANSDK.Program.ScanEvent += scanEventHandler;
        }
        scanDir = (string) input.scanDir;
        int result = await ScanAll(input);
        if(result == -1) return textBoxMessage;
        return "success";
    }

    // Open/Connect with the scanner
    private int OpenScanner()
    {
        //Console.WriteLine(TAG + "OpenScanner");
        string language = "0"; // 0 - English (default), 1 - Chinese Simplified, 2 - Chinese Traditional
        retVal = myKodakscansdk.SetLanguage(language);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetLanguage " + retVal.ToString("X");
            return -1;
        }

        retVal = myKodakscansdk.Init(hWnd);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: Init " + retVal.ToString("X");
            return -1;
        }

        string scannerName = myKodakscansdk.SelectScanner();
        textBoxMessage = "Tentando conexão com " + scannerName;

        string scanner = "KODAK Scanner: S2000";
        retVal = myKodakscansdk.SetScanner(scanner);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: StartScan " + retVal.ToString("X");
            return -1;
        }

        // Set the file name format
        string fileName = filePrefix + DateTime.Now.ToString("dd'-'MM'-'yyyy'-'HH'-'mm'-'ss") + "-"; // Default value: "img"
        retVal = myKodakscansdk.SetFileName(fileName);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetFileName " + retVal.ToString("X");
            return -1;
        }

        string filenumber = "0"; // Starting file number (from 0 to 999). Default: 0
        retVal = myKodakscansdk.SetFileNumber(filenumber);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetFileNumber " + retVal.ToString("X");
            return -1;
        }

        string filePathName = "\\\\172.16.0.19\\Desenvolvimento\\scanner\\" + scanDir; // Default: "C:\Twain"
        //if (filePathName.Substring(0, 2) != "C:") filePathName = Server.MapPath("~/" + filePath);
        retVal = myKodakscansdk.SetFilePathName(filePathName);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetFilePathName " + retVal.ToString("X");
            return -1;
        }

        retVal = myKodakscansdk.OpenScanner(hWnd);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: OpenScanner " + retVal.ToString("X");
            return -1;
        }

        string scannerProfile = "1"; // 1: Default profile (scanner profiles go from 1 to 99)
        retVal = myKodakscansdk.SetScannerProfile(scannerProfile);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetScannerProfile " + retVal.ToString("X");
            return -1;
        }
        textBoxMessage = "Conectado a " + scannerName;
        //Console.WriteLine(TAG + textBoxMessage);
        return 0;
    }

    // Start scanning
    private int StartScan()
    {
        //Console.WriteLine(TAG + "StartScan");
        string onePage = "0"; // 0 - Scan (Default), 1 - Scan only 1 Page
        retVal = myKodakscansdk.StartScan(onePage, hWnd);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: StartScan " + retVal.ToString("X");
            return - 1;
        }
        textBoxMessage = "Escaneamento em processo...";
        //Console.WriteLine(TAG + textBoxMessage);
        return 0;
    }

    // Stop scanning
    private int StopScan()
    {
        //Console.WriteLine(TAG + "StopScan");
        retVal = myKodakscansdk.StopScan();
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: StopScan " + retVal.ToString("X");
            return -1;
        }
        textBoxMessage = "Escaneamento terminado.";
        //Console.WriteLine(TAG + textBoxMessage);
        return 0;
    }

    // Close the connection to the scanner
    private int CloseScanner()
    {
        //Console.WriteLine(TAG + "CloseScanner");
        retVal = myKodakscansdk.CloseScanner();
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: CloseScanner " + retVal.ToString("X");
            return -1;
        }
        textBoxMessage = "Escaneamento terminado.";
        //Console.WriteLine(TAG + textBoxMessage);
        return 0;
    }

    // Check if the flatbed is attached
    private void IsFBAttached()
    {
        //Console.WriteLine(TAG + "IsFBAttached");
        retVal = myKodakscansdk.IsFBAttached();
        textBoxMessage = "IsFBAttached = " + retVal.ToString("X");
        //Console.WriteLine(TAG + textBoxMessage);
    }

    // Set the scanner parameters
    private int SetParameters()
    {
        //Console.WriteLine(TAG + "SetParameters");
        string paperSource = "3"; // 0 - Automatic, 1 - ADF Front Side, 2 - ADF Rear Side, 3 - ADF Duplex (Default), 4 - Flatbed
        retVal = myKodakscansdk.SetPaperSource(paperSource);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetPaperSource " + retVal.ToString("X");
            return -1;
        }

        string scanAs = "1"; // 0 - Black and White (Default), 1 - Grayscale, 2 - Color
        retVal = myKodakscansdk.SetScanAs(scanAs);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetScanAs " + retVal.ToString("X");
            return -1;
        }

        string documentType = "1"; // 0 - Photo, 1 - Text with Graphics (Default), 2 - Text with Photo, 3 - Text
        retVal = myKodakscansdk.SetDocumentType(documentType);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetDocumentType " + retVal.ToString("X");
            return -1;
        }

        string dpiResolution = "600"; // 100, 150, 200 (Default), 240, 250, 300, 400, 500, 600, 1200
        retVal = myKodakscansdk.SetDPIResolution(dpiResolution);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetDPIResolution " + retVal.ToString("X");
            return -1;
        }

        string fileType = "0"; // 0 - TIFF (Default), 1 - JPEG
        retVal = myKodakscansdk.SetFileType(fileType);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetFileType " + retVal.ToString("X");
            return -1;
        }

        string compressionType = "0"; // 0 - None (Default) [TIFF only], 5 - G4 [TIFF only], 6 - JPEG [JPEG only]
        retVal = myKodakscansdk.SetCompressionType(compressionType);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetCompressionType " + retVal.ToString("X");
            return -1;
        }

        if (compressionType == "6")
        {
            string jpegQuality = "100"; // 40 - Draft, 50 - Good (Default), 80 - Better, 90 - Best, 100 - Superior
            retVal = myKodakscansdk.SetJPEGQuality(jpegQuality);
            if (retVal != 0)
            {
                textBoxMessage = "Ocorreu um erro em: SetJPEGQuality " + retVal.ToString("X");
                return -1;
            }
        }

        if (scanAs != "0")
        {
            string sharpen = "0"; // 0 - None, 1 - Normal, 2 - High,  3 - Exaggerated
            retVal = myKodakscansdk.SetSharpen(sharpen);
            if (retVal != 0)
            {
                textBoxMessage = "Ocorreu um erro em: SetSharpen " + retVal.ToString("X");
                return -1;
            }
        }

        string imageRotation = "1"; // 0 – 0 degrees, 1 – Automatic (Default), 2 – None, 90 – 90 degrees, 180 – 180 degrees, 270 – 270 degrees, 360 – 360 degrees
        retVal = myKodakscansdk.SetImageRotation(imageRotation);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetImageRotation " + retVal.ToString("X");
            return -1;
        }

        // Only one type of rotation can be set
        //string orthogonalRotation = "0"
        // 0 – None, 1 – Automatic (Default), 2 – 90 degrees, 3 – 180 degrees, 4 – 270 degrees, 5 – Automatic - Default 90 degrees, 6 – Automatic - Default 180 degrees, 6 – Automatic - Default 270 degrees
        //retVal = myKodakscansdk.SetOrthogonalRotation(orthogonalRotation);
        //if (retVal != 0) {
        //    textBoxMessage = "Ocorreu um erro em: SetOrthogonalRotation " + retVal.ToString("X");
        //    return -1;
        //}

        string blankImageDeletion = "2"; // 1 - None (Default), 2 - Content
        retVal = myKodakscansdk.SetBlankImageDeletion(blankImageDeletion);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: SetBlankImageDeletion " + retVal.ToString("X");
            return -1;
        }

        if (blankImageDeletion == "2")
        {
            string blankImageDeletionPercent = "30"; // From 0~100%. Default: 0
            retVal = myKodakscansdk.SetBlankImageDeletionPercent(blankImageDeletionPercent);
            if (retVal != 0)
            {
                textBoxMessage = "Ocorreu um erro em: SetBlankImageDeletionPercent " + retVal.ToString("X");
                return -1;
            }
        }

        string showScannerUI = "0"; // 0 - Hide (default), 1 - Show
        retVal = myKodakscansdk.ShowScannerUI(showScannerUI);
        if (retVal != 0)
        {
            textBoxMessage = "Ocorreu um erro em: ShowScannerUI " + retVal.ToString("X");
            return -1;
        }
        textBoxMessage = "Configuração de scanner realizada.";
        //Console.WriteLine(TAG + textBoxMessage);
        return 0;
    }

    // The scan all click handler
    private async Task<int> ScanAll(dynamic input)
    {
        int result = CloseScanner();
        //if(result == -1) return -1;
        result = OpenScanner();
        if(result == -1) return -1;
        else input.log(textBoxMessage);
        result = SetParameters();
        if(result == -1) return -1;
        else input.log(textBoxMessage);
        result = StartScan();
        if(result == -1) return -1;
        else input.log(textBoxMessage);
        return 0;
    }
}