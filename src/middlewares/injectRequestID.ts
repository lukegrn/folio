import type { NextFunction, Request, Response } from "express";

export const injectRequestID = (
  _req: Request,
  res: Response,
  next: NextFunction,
) => {
  res.locals.id = crypto.randomUUID();

  next();
};
