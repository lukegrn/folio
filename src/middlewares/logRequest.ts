import type { NextFunction, Request, Response } from "express";
import { logInfoForRequest } from "../logger";
import type { LogBody } from "./types";

export const logRequest = (req: Request, res: Response, next: NextFunction) => {
  const startTime = Bun.nanoseconds() / 1000;

  const logBody: LogBody = {
    request: {
      path: req.path,
      method: req.method,
    },
    response: {
      status: 0,
      duration: 0,
    },
  };

  res.on("finish", () => {
    logBody.response.duration = Math.round(
      Bun.nanoseconds() / 1000 - startTime,
    );
    logBody.response.status = res.statusCode;

    logInfoForRequest(res, logBody);
  });

  next();
};
