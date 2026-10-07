import nodemailer from "nodemailer";
import { getConfig } from "../config/config";
import type { Response } from "express";
import { logErrorForRequest, logInfoForRequest } from "../logger";
import { getErrorMessage } from "../util/errors";

type Success = boolean;

interface SendOptions {
  from: string;
  to: string;
  subject: string;
  message: string;
  res: Response;
}

let transport: ReturnType<typeof nodemailer.createTransport>;

export const send = async ({
  from,
  to,
  subject,
  message,
  res,
}: SendOptions): Promise<Success> => {
  try {
    await transport.sendMail({
      from,
      to,
      subject,
      text: message,
      html: `<p>${message}</p>`,
    });

    logInfoForRequest(res, {
      msg: `Successfully sent email from ${from} to ${to}`,
    });

    return true;
  } catch (e: unknown) {
    logErrorForRequest(res, {
      error: `Failed to send email: ${getErrorMessage(e)}`,
    });

    return false;
  }
};

export const init = async () => {
  const conf = getConfig();

  transport = nodemailer.createTransport({
    host: conf.email.host,
    port: conf.email.port,
    secure: false,
    auth: {
      user: conf.email.user,
      pass: conf.email.pass,
    },
  });

  await transport.verify();
};
