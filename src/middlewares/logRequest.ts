import type { NextFunction, Request, Response } from "express";
import { log } from "../logger";
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
    id: res?.locals?.id,
  };

  res.on("finish", () => {
    logBody.response.duration = Math.round(
      Bun.nanoseconds() / 1000 - startTime,
    );
    logBody.response.status = res.statusCode;

    log.info(logBody);
  });

  next();
};
