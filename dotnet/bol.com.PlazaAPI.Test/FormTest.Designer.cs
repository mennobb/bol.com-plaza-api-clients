namespace bol.com.PlazaAPI.Test
{
    partial class FormTest
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.LaunchButton = new System.Windows.Forms.Button();
            this.CleanButton = new System.Windows.Forms.Button();
            this.groupBox1 = new System.Windows.Forms.GroupBox();
            this.optPayments = new System.Windows.Forms.RadioButton();
            this.optProcessOrdersOverview = new System.Windows.Forms.RadioButton();
            this.optShipmentsAndCancellations = new System.Windows.Forms.RadioButton();
            this.optOpenOrders = new System.Windows.Forms.RadioButton();
            this.label6 = new System.Windows.Forms.Label();
            this.txtUrl = new System.Windows.Forms.TextBox();
            this.label5 = new System.Windows.Forms.Label();
            this.txtSecretAccessKey = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.txtAccessKeyId = new System.Windows.Forms.TextBox();
            this.groupBox2 = new System.Windows.Forms.GroupBox();
            this.txtResponse = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.label1 = new System.Windows.Forms.Label();
            this.txtRequest = new System.Windows.Forms.TextBox();
            this.optShipmentsAndCancellationsXml = new System.Windows.Forms.RadioButton();
            this.groupBox1.SuspendLayout();
            this.groupBox2.SuspendLayout();
            this.SuspendLayout();
            // 
            // LaunchButton
            // 
            this.LaunchButton.Location = new System.Drawing.Point(817, 649);
            this.LaunchButton.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.LaunchButton.Name = "LaunchButton";
            this.LaunchButton.Size = new System.Drawing.Size(100, 28);
            this.LaunchButton.TabIndex = 2;
            this.LaunchButton.Text = "Launch";
            this.LaunchButton.UseVisualStyleBackColor = true;
            this.LaunchButton.Click += new System.EventHandler(this.LaunchButton_Click);
            // 
            // CleanButton
            // 
            this.CleanButton.Location = new System.Drawing.Point(925, 649);
            this.CleanButton.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.CleanButton.Name = "CleanButton";
            this.CleanButton.Size = new System.Drawing.Size(100, 28);
            this.CleanButton.TabIndex = 3;
            this.CleanButton.Text = "Clean";
            this.CleanButton.UseVisualStyleBackColor = true;
            this.CleanButton.Click += new System.EventHandler(this.CleanButton_Click);
            // 
            // groupBox1
            // 
            this.groupBox1.Controls.Add(this.optShipmentsAndCancellationsXml);
            this.groupBox1.Controls.Add(this.optPayments);
            this.groupBox1.Controls.Add(this.optProcessOrdersOverview);
            this.groupBox1.Controls.Add(this.optShipmentsAndCancellations);
            this.groupBox1.Controls.Add(this.optOpenOrders);
            this.groupBox1.Controls.Add(this.label6);
            this.groupBox1.Controls.Add(this.txtUrl);
            this.groupBox1.Controls.Add(this.label5);
            this.groupBox1.Controls.Add(this.txtSecretAccessKey);
            this.groupBox1.Controls.Add(this.label4);
            this.groupBox1.Controls.Add(this.txtAccessKeyId);
            this.groupBox1.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.groupBox1.Location = new System.Drawing.Point(16, 15);
            this.groupBox1.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.Padding = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBox1.Size = new System.Drawing.Size(1009, 218);
            this.groupBox1.TabIndex = 0;
            this.groupBox1.TabStop = false;
            // 
            // optPayments
            // 
            this.optPayments.AutoSize = true;
            this.optPayments.Location = new System.Drawing.Point(8, 186);
            this.optPayments.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.optPayments.Name = "optPayments";
            this.optPayments.Size = new System.Drawing.Size(91, 21);
            this.optPayments.TabIndex = 10;
            this.optPayments.Text = "Payments";
            this.optPayments.UseVisualStyleBackColor = true;
            // 
            // optProcessOrdersOverview
            // 
            this.optProcessOrdersOverview.AutoSize = true;
            this.optProcessOrdersOverview.Location = new System.Drawing.Point(7, 158);
            this.optProcessOrdersOverview.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.optProcessOrdersOverview.Name = "optProcessOrdersOverview";
            this.optProcessOrdersOverview.Size = new System.Drawing.Size(190, 21);
            this.optProcessOrdersOverview.TabIndex = 9;
            this.optProcessOrdersOverview.Text = "Process Orders Overview";
            this.optProcessOrdersOverview.UseVisualStyleBackColor = true;
            // 
            // optShipmentsAndCancellations
            // 
            this.optShipmentsAndCancellations.AutoSize = true;
            this.optShipmentsAndCancellations.Location = new System.Drawing.Point(8, 100);
            this.optShipmentsAndCancellations.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.optShipmentsAndCancellations.Name = "optShipmentsAndCancellations";
            this.optShipmentsAndCancellations.Size = new System.Drawing.Size(211, 21);
            this.optShipmentsAndCancellations.TabIndex = 7;
            this.optShipmentsAndCancellations.Text = "Shipments and Cancellations";
            this.optShipmentsAndCancellations.UseVisualStyleBackColor = true;
            // 
            // optOpenOrders
            // 
            this.optOpenOrders.AutoSize = true;
            this.optOpenOrders.Checked = true;
            this.optOpenOrders.Location = new System.Drawing.Point(8, 71);
            this.optOpenOrders.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.optOpenOrders.Name = "optOpenOrders";
            this.optOpenOrders.Size = new System.Drawing.Size(112, 21);
            this.optOpenOrders.TabIndex = 6;
            this.optOpenOrders.TabStop = true;
            this.optOpenOrders.Text = "Open Orders";
            this.optOpenOrders.UseVisualStyleBackColor = true;
            // 
            // label6
            // 
            this.label6.AutoSize = true;
            this.label6.ForeColor = System.Drawing.SystemColors.HotTrack;
            this.label6.Location = new System.Drawing.Point(340, 20);
            this.label6.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(36, 17);
            this.label6.TabIndex = 2;
            this.label6.Text = "URL";
            // 
            // txtUrl
            // 
            this.txtUrl.Location = new System.Drawing.Point(344, 39);
            this.txtUrl.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.txtUrl.Name = "txtUrl";
            this.txtUrl.Size = new System.Drawing.Size(656, 23);
            this.txtUrl.TabIndex = 5;
            this.txtUrl.Text = "https://test-plazaapi.bol.com";
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.ForeColor = System.Drawing.SystemColors.HotTrack;
            this.label5.Location = new System.Drawing.Point(172, 20);
            this.label5.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(126, 17);
            this.label5.TabIndex = 1;
            this.label5.Text = "Secret Access Key";
            // 
            // txtSecretAccessKey
            // 
            this.txtSecretAccessKey.Location = new System.Drawing.Point(176, 39);
            this.txtSecretAccessKey.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.txtSecretAccessKey.Name = "txtSecretAccessKey";
            this.txtSecretAccessKey.Size = new System.Drawing.Size(159, 23);
            this.txtSecretAccessKey.TabIndex = 4;
            this.txtSecretAccessKey.Text = "PR1v@K3YT35T";
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.ForeColor = System.Drawing.SystemColors.HotTrack;
            this.label4.Location = new System.Drawing.Point(4, 20);
            this.label4.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(96, 17);
            this.label4.TabIndex = 0;
            this.label4.Text = "Access Key Id";
            // 
            // txtAccessKeyId
            // 
            this.txtAccessKeyId.Location = new System.Drawing.Point(8, 39);
            this.txtAccessKeyId.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.txtAccessKeyId.Name = "txtAccessKeyId";
            this.txtAccessKeyId.Size = new System.Drawing.Size(159, 23);
            this.txtAccessKeyId.TabIndex = 3;
            this.txtAccessKeyId.Text = "PuBL1CKEYT35T";
            // 
            // groupBox2
            // 
            this.groupBox2.Controls.Add(this.txtResponse);
            this.groupBox2.Controls.Add(this.label2);
            this.groupBox2.Controls.Add(this.label1);
            this.groupBox2.Controls.Add(this.txtRequest);
            this.groupBox2.Location = new System.Drawing.Point(16, 241);
            this.groupBox2.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBox2.Name = "groupBox2";
            this.groupBox2.Padding = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.groupBox2.Size = new System.Drawing.Size(1009, 400);
            this.groupBox2.TabIndex = 1;
            this.groupBox2.TabStop = false;
            // 
            // txtResponse
            // 
            this.txtResponse.Location = new System.Drawing.Point(12, 187);
            this.txtResponse.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.txtResponse.Multiline = true;
            this.txtResponse.Name = "txtResponse";
            this.txtResponse.ScrollBars = System.Windows.Forms.ScrollBars.Both;
            this.txtResponse.Size = new System.Drawing.Size(988, 205);
            this.txtResponse.TabIndex = 3;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.ForeColor = System.Drawing.SystemColors.HotTrack;
            this.label2.Location = new System.Drawing.Point(8, 167);
            this.label2.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(72, 17);
            this.label2.TabIndex = 2;
            this.label2.Text = "Response";
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.ForeColor = System.Drawing.SystemColors.HotTrack;
            this.label1.Location = new System.Drawing.Point(8, 20);
            this.label1.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(61, 17);
            this.label1.TabIndex = 0;
            this.label1.Text = "Request";
            // 
            // txtRequest
            // 
            this.txtRequest.Location = new System.Drawing.Point(12, 41);
            this.txtRequest.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.txtRequest.Multiline = true;
            this.txtRequest.Name = "txtRequest";
            this.txtRequest.ScrollBars = System.Windows.Forms.ScrollBars.Both;
            this.txtRequest.Size = new System.Drawing.Size(988, 122);
            this.txtRequest.TabIndex = 1;
            // 
            // optShipmentsAndCancellationsXml
            // 
            this.optShipmentsAndCancellationsXml.AutoSize = true;
            this.optShipmentsAndCancellationsXml.Location = new System.Drawing.Point(7, 129);
            this.optShipmentsAndCancellationsXml.Margin = new System.Windows.Forms.Padding(4);
            this.optShipmentsAndCancellationsXml.Name = "optShipmentsAndCancellationsXml";
            this.optShipmentsAndCancellationsXml.Size = new System.Drawing.Size(311, 21);
            this.optShipmentsAndCancellationsXml.TabIndex = 8;
            this.optShipmentsAndCancellationsXml.Text = "Shipments and Cancellations (from a xml file)";
            this.optShipmentsAndCancellationsXml.UseVisualStyleBackColor = true;
            // 
            // FormTest
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(8F, 16F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(1045, 692);
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Controls.Add(this.CleanButton);
            this.Controls.Add(this.LaunchButton);
            this.Margin = new System.Windows.Forms.Padding(4, 4, 4, 4);
            this.Name = "FormTest";
            this.Text = "bol.com Plaza API Client Test";
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            this.groupBox2.ResumeLayout(false);
            this.groupBox2.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Button LaunchButton;
        private System.Windows.Forms.Button CleanButton;
        private System.Windows.Forms.GroupBox groupBox1;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.TextBox txtUrl;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox txtSecretAccessKey;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.TextBox txtAccessKeyId;
        private System.Windows.Forms.GroupBox groupBox2;
        private System.Windows.Forms.TextBox txtResponse;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox txtRequest;
        private System.Windows.Forms.RadioButton optPayments;
        private System.Windows.Forms.RadioButton optProcessOrdersOverview;
        private System.Windows.Forms.RadioButton optShipmentsAndCancellations;
        private System.Windows.Forms.RadioButton optOpenOrders;
        private System.Windows.Forms.RadioButton optShipmentsAndCancellationsXml;
    }
}

