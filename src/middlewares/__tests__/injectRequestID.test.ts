import type { NextFunction, Request, Response } from "express";
import { test, expect } from "bun:test";
import { injectRequestID } from "../injectRequestID";

const mockInputs = () => ({
  req: {
    path: "/",
    method: "GET",
  } as Request,
  res: {
    locals: {},
  } as unknown as Response,
  nextFn: () => ({}) as NextFunction,
});

test("Injects request ID as UUID", () => {
  const { req, res, nextFn } = mockInputs();
  const uuidRegex =
    /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

  injectRequestID(req, res, nextFn);

  expect(res.locals.id).toMatch(uuidRegex);
});
